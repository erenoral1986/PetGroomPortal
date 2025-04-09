from flask_wtf import FlaskForm
from wtforms import StringField, PasswordField, SubmitField, BooleanField, TextAreaField, SelectField, FloatField, TimeField, IntegerField, DateField, HiddenField
from wtforms.validators import DataRequired, Email, Length, EqualTo, ValidationError, Optional
from models import User

class RegistrationForm(FlaskForm):
    username = StringField('Username', validators=[DataRequired(), Length(min=3, max=20)])
    email = StringField('Email', validators=[DataRequired(), Email()])
    password = PasswordField('Password', validators=[DataRequired(), Length(min=6)])
    confirm_password = PasswordField('Confirm Password', validators=[DataRequired(), EqualTo('password')])
    first_name = StringField('First Name', validators=[DataRequired()])
    last_name = StringField('Last Name', validators=[DataRequired()])
    phone = StringField('Phone Number', validators=[DataRequired()])
    submit = SubmitField('Sign Up')
    
    def validate_username(self, username):
        user = User.query.filter_by(username=username.data).first()
        if user:
            raise ValidationError('Username is already taken. Please choose another one.')
            
    def validate_email(self, email):
        user = User.query.filter_by(email=email.data).first()
        if user:
            raise ValidationError('Email is already registered. Please use a different one.')

class LoginForm(FlaskForm):
    email = StringField('Email', validators=[DataRequired(), Email()])
    password = PasswordField('Password', validators=[DataRequired()])
    remember = BooleanField('Remember Me')
    submit = SubmitField('Login')

class SalonSearchForm(FlaskForm):
    location = StringField('Şehir', validators=[DataRequired()])
    district = SelectField('İlçe', choices=[('all', 'Tüm İlçeler')], validate_choice=False)
    pet_type = SelectField('Evcil Hayvan Türü', choices=[
        ('all', 'Tüm Evcil Hayvanlar'),
        ('dog', 'Köpek'),
        ('cat', 'Kedi'),
        ('both', 'Kedi ve Köpek')
    ])
    submit = SubmitField('Search')

class ServiceForm(FlaskForm):
    name = StringField('Service Name', validators=[DataRequired()])
    description = TextAreaField('Description')
    price = FloatField('Price', validators=[DataRequired()])
    duration = IntegerField('Duration (minutes)', validators=[DataRequired()])
    pet_type = SelectField('Pet Type', choices=[('dog', 'Dog'), ('cat', 'Cat'), ('both', 'Both')])
    submit = SubmitField('Save Service')

class AvailabilityForm(FlaskForm):
    day_of_week = SelectField('Day', choices=[
        (0, 'Monday'), (1, 'Tuesday'), (2, 'Wednesday'),
        (3, 'Thursday'), (4, 'Friday'), (5, 'Saturday'), (6, 'Sunday')
    ], coerce=int)
    start_time = TimeField('Start Time', validators=[DataRequired()])
    end_time = TimeField('End Time', validators=[DataRequired()])
    submit = SubmitField('Save Availability')

class AppointmentForm(FlaskForm):
    service_id = SelectField('Service', coerce=int, validators=[DataRequired()])
    date = DateField('Date', validators=[DataRequired()])
    time_slot = SelectField('Time', validators=[DataRequired()])
    pet_name = StringField('Pet Name', validators=[DataRequired()])
    pet_type = SelectField('Pet Type', choices=[('dog', 'Dog'), ('cat', 'Cat'), ('other', 'Other')])
    pet_breed = StringField('Pet Breed')
    notes = TextAreaField('Special Notes or Requests')
    submit = SubmitField('Book Appointment')

class SalonProfileForm(FlaskForm):
    name = StringField('Salon Name', validators=[DataRequired()])
    address = StringField('Address', validators=[DataRequired()])
    city = StringField('City', validators=[DataRequired()])
    zip_code = StringField('ZIP Code', validators=[DataRequired()])
    phone = StringField('Phone', validators=[DataRequired()])
    email = StringField('Email', validators=[DataRequired(), Email()])
    description = TextAreaField('Description')
    opens_at = TimeField('Opening Time', validators=[DataRequired()])
    closes_at = TimeField('Closing Time', validators=[DataRequired()])
    latitude = FloatField('Latitude', validators=[Optional()])
    longitude = FloatField('Longitude', validators=[Optional()])
    submit = SubmitField('Save Salon Details')

class AppointmentStatusForm(FlaskForm):
    status = SelectField('Status', choices=[
        ('pending', 'Pending'),
        ('confirmed', 'Confirmed'),
        ('cancelled', 'Cancelled'),
        ('completed', 'Completed')
    ])
    notes = TextAreaField('Notes')
    submit = SubmitField('Update Status')

class ProfileUpdateForm(FlaskForm):
    first_name = StringField('First Name', validators=[DataRequired()])
    last_name = StringField('Last Name', validators=[DataRequired()])
    phone = StringField('Phone Number', validators=[DataRequired()])
    email = StringField('Email', validators=[DataRequired(), Email()])
    submit = SubmitField('Update Profile')
