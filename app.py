import os
import logging

from flask import Flask, flash
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy.orm import DeclarativeBase
from werkzeug.middleware.proxy_fix import ProxyFix
from flask_login import LoginManager, login_user, current_user
from flask_dance.contrib.google import make_google_blueprint, google

# Set up logging
logging.basicConfig(level=logging.DEBUG)

class Base(DeclarativeBase):
    pass

# Initialize SQLAlchemy
db = SQLAlchemy(model_class=Base)

# Create the app
app = Flask(__name__)
app.secret_key = os.environ.get("SESSION_SECRET", "dev-secret-key")
app.wsgi_app = ProxyFix(app.wsgi_app, x_proto=1, x_host=1)  # needed for url_for to generate with https

# Configure the database
app.config["SQLALCHEMY_DATABASE_URI"] = os.environ.get("DATABASE_URL", "sqlite:///pet_kuafor.db")
app.config["SQLALCHEMY_ENGINE_OPTIONS"] = {
    "pool_recycle": 300,
    "pool_pre_ping": True,
}
app.config["SQLALCHEMY_TRACK_MODIFICATIONS"] = False

# Initialize the database
db.init_app(app)

# Initialize login manager
login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = 'login'
login_manager.login_message_category = 'info'


# Google OAuth Configuration
os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'  # Development only
GOOGLE_CLIENT_ID = os.environ.get("GOOGLE_CLIENT_ID", "your-client-id")
GOOGLE_CLIENT_SECRET = os.environ.get("GOOGLE_CLIENT_SECRET", "your-client-secret")

# Set up Google OAuth
google_bp = make_google_blueprint(
    client_id=GOOGLE_CLIENT_ID,
    client_secret=GOOGLE_CLIENT_SECRET,
    scope=['profile', 'email'],
    redirect_to='google_authorized'
)
app.register_blueprint(google_bp, url_prefix='/login')

@app.route('/login/google/authorized')
def google_authorized():
    if not google.authorized:
        flash('Google ile giriş başarısız.', 'danger')
        return redirect(url_for('index'))
        
    resp = google.get('/oauth2/v2/userinfo')

    access_token = resp['access_token']
    resp = google.get('/oauth2/v2/userinfo', token=access_token)
    if resp.ok:
        google_info = resp.json()
        user = User.query.filter_by(email=google_info['email']).first()
        if user:
            login_user(user)
            flash('Google ile giriş başarılı!', 'success')
            return redirect(url_for('index'))  # Redirect to your home page
        else:
            # Handle new user registration if needed.  This is a simplified example
            flash('Google hesabınız ile kayıtlı değilsiniz.', 'warning')
            return redirect(url_for('register')) # Redirect to registration page.
    else:
        flash('Google bilgileri alınamadı.', 'danger')
        return redirect(url_for('index'))


# Import models and routes after app is created to avoid circular imports
with app.app_context():
    # Import models
    from models import User, Salon, Service, Appointment, Availability

    # Create all tables
    db.create_all()

    # Import routes
    from routes import *