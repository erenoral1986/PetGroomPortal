from flask import render_template, url_for, flash, redirect, request, jsonify, abort
from flask_login import login_user, current_user, logout_user, login_required
from sqlalchemy import func
from datetime import datetime, timedelta, time
import json

from app import app, db
from models import User, Salon, Service, Appointment, Availability
from forms import (
    RegistrationForm, LoginForm, SalonSearchForm, ServiceForm, 
    AvailabilityForm, AppointmentForm, SalonProfileForm,
    AppointmentStatusForm, ProfileUpdateForm
)

# Common data for navbar
def get_base_data():
    return {
        'is_authenticated': current_user.is_authenticated,
        'is_admin': current_user.is_authenticated and current_user.role == 'admin',
        'is_salon_owner': current_user.is_authenticated and current_user.role == 'salon_owner'
    }

# Helper function to convert time objects to strings for JSON
def time_to_str(t):
    if t is None:
        return None
    return t.strftime('%H:%M')

# Home route
@app.route('/')
def index():
    base_data = get_base_data()
    search_form = SalonSearchForm()
    return render_template('index.html', title='Pet Grooming - Home', form=search_form, **base_data)

# User registration
@app.route('/register', methods=['GET', 'POST'])
def register():
    if current_user.is_authenticated:
        return redirect(url_for('index'))
    
    form = RegistrationForm()
    if form.validate_on_submit():
        user = User(
            username=form.username.data,
            email=form.email.data,
            first_name=form.first_name.data,
            last_name=form.last_name.data,
            phone=form.phone.data,
            role='customer'
        )
        user.set_password(form.password.data)
        db.session.add(user)
        db.session.commit()
        
        flash('Your account has been created! You can now log in.', 'success')
        return redirect(url_for('login'))
    
    return render_template('register.html', title='Register', form=form, **get_base_data())

# User login
@app.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('index'))
    
    form = LoginForm()
    if form.validate_on_submit():
        user = User.query.filter_by(email=form.email.data).first()
        
        if user and user.check_password(form.password.data):
            login_user(user, remember=form.remember.data)
            next_page = request.args.get('next')
            
            # Redirect based on user role
            if user.role == 'salon_owner':
                if next_page:
                    return redirect(next_page)
                return redirect(url_for('admin_dashboard'))
            else:
                if next_page:
                    return redirect(next_page)
                return redirect(url_for('index'))
        else:
            flash('Login unsuccessful. Please check email and password.', 'danger')
    
    return render_template('login.html', title='Login', form=form, **get_base_data())

# User logout
@app.route('/logout')
def logout():
    logout_user()
    return redirect(url_for('index'))

# User profile
@app.route('/profile', methods=['GET', 'POST'])
@login_required
def profile():
    form = ProfileUpdateForm()
    
    if form.validate_on_submit():
        current_user.first_name = form.first_name.data
        current_user.last_name = form.last_name.data
        current_user.phone = form.phone.data
        current_user.email = form.email.data
        db.session.commit()
        flash('Your profile has been updated!', 'success')
        return redirect(url_for('profile'))
    elif request.method == 'GET':
        form.first_name.data = current_user.first_name
        form.last_name.data = current_user.last_name
        form.phone.data = current_user.phone
        form.email.data = current_user.email
    
    return render_template('profile.html', title='Profile', form=form, **get_base_data())

# Search for salons
@app.route('/salons', methods=['GET', 'POST'])
def salons():
    form = SalonSearchForm()
    
    if form.validate_on_submit() or request.args.get('location'):
        location = form.location.data if form.validate_on_submit() else request.args.get('location')
        pet_type = form.pet_type.data if form.validate_on_submit() else request.args.get('pet_type', 'all')
        
        # Şehir filtresi (basit bir "içerir" araması)
        salons = Salon.query.filter(
            (Salon.city.ilike(f'%{location}%')) | 
            (Salon.zip_code == location) |
            (Salon.address.ilike(f'%{location}%'))
        ).all()
        
        # Evcil hayvan türüne göre filtreleme
        if pet_type != 'all':
            # İlgili hizmetleri olan salonları filtreleyerek getir
            filtered_salons = []
            for salon in salons:
                has_services = False
                for service in salon.services:
                    if pet_type == 'both' and service.pet_type == 'both':
                        has_services = True
                        break
                    elif pet_type == 'dog' and (service.pet_type == 'dog' or service.pet_type == 'both'):
                        has_services = True
                        break
                    elif pet_type == 'cat' and (service.pet_type == 'cat' or service.pet_type == 'both'):
                        has_services = True
                        break
                if has_services:
                    filtered_salons.append(salon)
            salons = filtered_salons
        
        return render_template('salons.html', title='Salons', salons=salons, form=form, 
                              location=location, pet_type=pet_type, search_performed=True, **get_base_data())
    
    salons = Salon.query.all()
    return render_template('salons.html', title='Salons', salons=salons, form=form, 
                          search_performed=False, **get_base_data())

# Salon details
@app.route('/salon/<int:salon_id>')
def salon_detail(salon_id):
    salon = Salon.query.get_or_404(salon_id)
    services = Service.query.filter_by(salon_id=salon_id).all()
    return render_template('salon_detail.html', title=salon.name, salon=salon, services=services, **get_base_data())

# Book appointment
@app.route('/salon/<int:salon_id>/book', methods=['GET', 'POST'])
@login_required
def book_appointment(salon_id):
    salon = Salon.query.get_or_404(salon_id)
    services = Service.query.filter_by(salon_id=salon_id).all()
    
    form = AppointmentForm()
    form.service_id.choices = [(service.id, f"{service.name} - ${service.price}") for service in services]
    
    if form.validate_on_submit():
        service = Service.query.get(form.service_id.data)
        time_parts = form.time_slot.data.split(':')
        start_hour, start_minute = int(time_parts[0]), int(time_parts[1])
        
        start_time = time(start_hour, start_minute)
        
        # Calculate end time based on service duration
        start_datetime = datetime.combine(datetime.today(), start_time)
        end_datetime = start_datetime + timedelta(minutes=service.duration)
        end_time = end_datetime.time()
        
        appointment = Appointment(
            user_id=current_user.id,
            salon_id=salon_id,
            service_id=form.service_id.data,
            date=form.date.data,
            start_time=start_time,
            end_time=end_time,
            pet_name=form.pet_name.data,
            pet_type=form.pet_type.data,
            pet_breed=form.pet_breed.data,
            notes=form.notes.data,
            status='pending'
        )
        
        db.session.add(appointment)
        db.session.commit()
        
        flash('Your appointment has been booked! The salon will confirm your appointment soon.', 'success')
        return redirect(url_for('bookings'))
    
    return render_template('booking.html', title='Book Appointment', 
                          salon=salon, form=form, salon_id=salon_id, **get_base_data())

# Get available time slots for a specific date and salon
@app.route('/salon/<int:salon_id>/available_slots', methods=['POST'])
@login_required
def available_slots(salon_id):
    salon = Salon.query.get_or_404(salon_id)
    data = request.get_json()
    
    if not data or 'date' not in data or 'service_id' not in data:
        return jsonify({'error': 'Missing required data'}), 400
    
    date_str = data['date']
    service_id = data['service_id']
    
    try:
        selected_date = datetime.strptime(date_str, '%Y-%m-%d').date()
        day_of_week = selected_date.weekday()  # 0-6, Monday is 0
        
        service = Service.query.get(service_id)
        if not service:
            return jsonify({'error': 'Service not found'}), 404
        
        # Get availability for the day of week
        availability = Availability.query.filter_by(
            salon_id=salon_id,
            day_of_week=day_of_week
        ).first()
        
        if not availability:
            return jsonify({'slots': []})
        
        # Get all appointments for the selected date
        appointments = Appointment.query.filter(
            Appointment.salon_id == salon_id,
            Appointment.date == selected_date,
            Appointment.status != 'cancelled'
        ).all()
        
        # Generate time slots based on salon hours and service duration
        slots = []
        
        # Convert availability times to datetime for easier manipulation
        start_datetime = datetime.combine(selected_date, availability.start_time)
        end_datetime = datetime.combine(selected_date, availability.end_time)
        
        # Generate slots in service duration increments
        current_slot = start_datetime
        while current_slot + timedelta(minutes=service.duration) <= end_datetime:
            # Check if the slot is already booked
            slot_end = current_slot + timedelta(minutes=service.duration)
            
            is_available = True
            for appt in appointments:
                appt_start = datetime.combine(selected_date, appt.start_time)
                appt_end = datetime.combine(selected_date, appt.end_time)
                
                # Check if there's an overlap
                if (current_slot < appt_end and slot_end > appt_start):
                    is_available = False
                    break
            
            if is_available:
                slots.append(current_slot.strftime('%H:%M'))
            
            # Move to next slot (30-minute increments)
            current_slot += timedelta(minutes=30)
        
        return jsonify({'slots': slots})
    
    except ValueError:
        return jsonify({'error': 'Invalid date format'}), 400

# User's booked appointments
@app.route('/bookings')
@login_required
def bookings():
    appointments = Appointment.query.filter_by(user_id=current_user.id).order_by(Appointment.date.desc()).all()
    
    # Enhance appointments with salon and service info
    appointment_data = []
    for appointment in appointments:
        salon = Salon.query.get(appointment.salon_id)
        service = Service.query.get(appointment.service_id)
        
        appointment_data.append({
            'appointment': appointment,
            'salon': salon,
            'service': service
        })
    
    return render_template('bookings.html', title='My Appointments', 
                          appointments=appointment_data, **get_base_data())

# Cancel appointment
@app.route('/appointment/<int:appointment_id>/cancel', methods=['POST'])
@login_required
def cancel_appointment(appointment_id):
    appointment = Appointment.query.get_or_404(appointment_id)
    
    # Check if the appointment belongs to the current user
    if appointment.user_id != current_user.id:
        abort(403)
    
    appointment.status = 'cancelled'
    db.session.commit()
    
    flash('Your appointment has been cancelled.', 'success')
    return redirect(url_for('bookings'))

# Admin dashboard
@app.route('/admin')
@login_required
def admin_dashboard():
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    if current_user.role == 'salon_owner':
        salon = Salon.query.get(current_user.salon_id)
        if not salon:
            # Create a new salon for the owner
            return redirect(url_for('admin_create_salon'))
        
        # Get stats for the salon
        total_appointments = Appointment.query.filter_by(salon_id=salon.id).count()
        upcoming_appointments = Appointment.query.filter(
            Appointment.salon_id == salon.id,
            Appointment.date >= datetime.today().date(),
            Appointment.status != 'cancelled'
        ).count()
        total_services = Service.query.filter_by(salon_id=salon.id).count()
        
        stats = {
            'total_appointments': total_appointments,
            'upcoming_appointments': upcoming_appointments,
            'total_services': total_services
        }
        
        return render_template('admin/dashboard.html', title='Admin Dashboard', 
                              salon=salon, stats=stats, **get_base_data())
    else:
        # Admin sees all salons
        salons = Salon.query.all()
        total_users = User.query.count()
        total_appointments = Appointment.query.count()
        
        stats = {
            'total_salons': len(salons),
            'total_users': total_users,
            'total_appointments': total_appointments
        }
        
        return render_template('admin/dashboard.html', title='Admin Dashboard', 
                              salons=salons, stats=stats, **get_base_data())

# Admin create salon
@app.route('/admin/salon/create', methods=['GET', 'POST'])
@login_required
def admin_create_salon():
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    form = SalonProfileForm()
    
    if form.validate_on_submit():
        salon = Salon(
            name=form.name.data,
            address=form.address.data,
            city=form.city.data,
            zip_code=form.zip_code.data,
            phone=form.phone.data,
            email=form.email.data,
            description=form.description.data,
            opens_at=form.opens_at.data,
            closes_at=form.closes_at.data,
            latitude=form.latitude.data,
            longitude=form.longitude.data
        )
        
        db.session.add(salon)
        db.session.commit()
        
        # Associate the salon with the current user
        if current_user.role == 'salon_owner':
            current_user.salon_id = salon.id
            db.session.commit()
        
        flash('Salon has been created successfully!', 'success')
        return redirect(url_for('admin_dashboard'))
    
    return render_template('admin/services.html', title='Create Salon', 
                          form=form, create_mode=True, **get_base_data())

# Admin edit salon
@app.route('/admin/salon/edit', methods=['GET', 'POST'])
@login_required
def admin_edit_salon():
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    if current_user.role == 'salon_owner':
        salon = Salon.query.get(current_user.salon_id)
        if not salon:
            return redirect(url_for('admin_create_salon'))
    else:
        salon_id = request.args.get('salon_id', type=int)
        if not salon_id:
            abort(400)
        salon = Salon.query.get_or_404(salon_id)
    
    form = SalonProfileForm()
    
    if form.validate_on_submit():
        salon.name = form.name.data
        salon.address = form.address.data
        salon.city = form.city.data
        salon.zip_code = form.zip_code.data
        salon.phone = form.phone.data
        salon.email = form.email.data
        salon.description = form.description.data
        salon.opens_at = form.opens_at.data
        salon.closes_at = form.closes_at.data
        salon.latitude = form.latitude.data
        salon.longitude = form.longitude.data
        
        db.session.commit()
        flash('Salon information has been updated!', 'success')
        return redirect(url_for('admin_dashboard'))
    
    elif request.method == 'GET':
        form.name.data = salon.name
        form.address.data = salon.address
        form.city.data = salon.city
        form.zip_code.data = salon.zip_code
        form.phone.data = salon.phone
        form.email.data = salon.email
        form.description.data = salon.description
        form.opens_at.data = salon.opens_at
        form.closes_at.data = salon.closes_at
        form.latitude.data = salon.latitude
        form.longitude.data = salon.longitude
    
    return render_template('admin/services.html', title='Edit Salon', 
                          form=form, salon=salon, **get_base_data())

# Admin manage services
@app.route('/admin/services', methods=['GET', 'POST'])
@login_required
def admin_services():
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    if current_user.role == 'salon_owner':
        salon = Salon.query.get(current_user.salon_id)
        if not salon:
            flash('Please create your salon first.', 'warning')
            return redirect(url_for('admin_create_salon'))
    else:
        salon_id = request.args.get('salon_id', type=int)
        if not salon_id:
            abort(400)
        salon = Salon.query.get_or_404(salon_id)
    
    # Get existing services
    services = Service.query.filter_by(salon_id=salon.id).all()
    
    # Handle new service form
    form = ServiceForm()
    
    if form.validate_on_submit():
        service = Service(
            salon_id=salon.id,
            name=form.name.data,
            description=form.description.data,
            price=form.price.data,
            duration=form.duration.data,
            pet_type=form.pet_type.data
        )
        
        db.session.add(service)
        db.session.commit()
        
        flash('New service has been added!', 'success')
        return redirect(url_for('admin_services'))
    
    return render_template('admin/services.html', title='Manage Services', 
                          salon=salon, services=services, form=form, **get_base_data())

# Admin edit service
@app.route('/admin/service/<int:service_id>/edit', methods=['GET', 'POST'])
@login_required
def admin_edit_service(service_id):
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    service = Service.query.get_or_404(service_id)
    
    # Verify ownership
    if current_user.role == 'salon_owner' and service.salon_id != current_user.salon_id:
        abort(403)
    
    form = ServiceForm()
    
    if form.validate_on_submit():
        service.name = form.name.data
        service.description = form.description.data
        service.price = form.price.data
        service.duration = form.duration.data
        service.pet_type = form.pet_type.data
        
        db.session.commit()
        flash('Service has been updated!', 'success')
        return redirect(url_for('admin_services'))
    
    elif request.method == 'GET':
        form.name.data = service.name
        form.description.data = service.description
        form.price.data = service.price
        form.duration.data = service.duration
        form.pet_type.data = service.pet_type
    
    return render_template('admin/services.html', title='Edit Service', 
                          form=form, service=service, edit_mode=True, **get_base_data())

# Admin delete service
@app.route('/admin/service/<int:service_id>/delete', methods=['POST'])
@login_required
def admin_delete_service(service_id):
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    service = Service.query.get_or_404(service_id)
    
    # Verify ownership
    if current_user.role == 'salon_owner' and service.salon_id != current_user.salon_id:
        abort(403)
    
    db.session.delete(service)
    db.session.commit()
    
    flash('Service has been deleted!', 'success')
    return redirect(url_for('admin_services'))

# Admin manage availability
@app.route('/admin/availability', methods=['GET', 'POST'])
@login_required
def admin_availability():
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    if current_user.role == 'salon_owner':
        salon = Salon.query.get(current_user.salon_id)
        if not salon:
            flash('Please create your salon first.', 'warning')
            return redirect(url_for('admin_create_salon'))
    else:
        salon_id = request.args.get('salon_id', type=int)
        if not salon_id:
            abort(400)
        salon = Salon.query.get_or_404(salon_id)
    
    # Get existing availability
    availability = Availability.query.filter_by(salon_id=salon.id).order_by(Availability.day_of_week).all()
    
    # Handle new availability form
    form = AvailabilityForm()
    
    if form.validate_on_submit():
        # Check if there's already an entry for this day
        existing = Availability.query.filter_by(
            salon_id=salon.id,
            day_of_week=form.day_of_week.data
        ).first()
        
        if existing:
            existing.start_time = form.start_time.data
            existing.end_time = form.end_time.data
        else:
            new_availability = Availability(
                salon_id=salon.id,
                day_of_week=form.day_of_week.data,
                start_time=form.start_time.data,
                end_time=form.end_time.data
            )
            db.session.add(new_availability)
        
        db.session.commit()
        flash('Availability has been updated!', 'success')
        return redirect(url_for('admin_availability'))
    
    # For display purposes, create a dict with all days
    days_dict = {
        0: {'name': 'Monday', 'available': False},
        1: {'name': 'Tuesday', 'available': False},
        2: {'name': 'Wednesday', 'available': False},
        3: {'name': 'Thursday', 'available': False},
        4: {'name': 'Friday', 'available': False},
        5: {'name': 'Saturday', 'available': False},
        6: {'name': 'Sunday', 'available': False}
    }
    
    for day in availability:
        days_dict[day.day_of_week]['available'] = True
        days_dict[day.day_of_week]['start_time'] = day.start_time
        days_dict[day.day_of_week]['end_time'] = day.end_time
    
    return render_template('admin/availability.html', title='Manage Availability', 
                          salon=salon, days=days_dict, form=form, **get_base_data())

# Admin delete availability
@app.route('/admin/availability/<int:day_of_week>/delete', methods=['POST'])
@login_required
def admin_delete_availability(day_of_week):
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    if current_user.role == 'salon_owner':
        salon_id = current_user.salon_id
    else:
        salon_id = request.form.get('salon_id', type=int)
        if not salon_id:
            abort(400)
    
    availability = Availability.query.filter_by(
        salon_id=salon_id,
        day_of_week=day_of_week
    ).first_or_404()
    
    db.session.delete(availability)
    db.session.commit()
    
    flash('Availability has been removed!', 'success')
    return redirect(url_for('admin_availability'))

# Admin manage appointments
@app.route('/admin/appointments')
@login_required
def admin_appointments():
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    if current_user.role == 'salon_owner':
        salon = Salon.query.get(current_user.salon_id)
        if not salon:
            flash('Please create your salon first.', 'warning')
            return redirect(url_for('admin_create_salon'))
        
        appointments = Appointment.query.filter_by(salon_id=salon.id).order_by(Appointment.date.desc()).all()
    else:
        salon_id = request.args.get('salon_id', type=int)
        if salon_id:
            salon = Salon.query.get_or_404(salon_id)
            appointments = Appointment.query.filter_by(salon_id=salon_id).order_by(Appointment.date.desc()).all()
        else:
            # Admin sees all appointments
            appointments = Appointment.query.order_by(Appointment.date.desc()).all()
            salon = None
    
    # Enhance appointments with user and service info
    appointment_data = []
    for appointment in appointments:
        user = User.query.get(appointment.user_id)
        service = Service.query.get(appointment.service_id)
        
        appointment_data.append({
            'appointment': appointment,
            'user': user,
            'service': service
        })
    
    return render_template('admin/appointments.html', title='Manage Appointments', 
                          appointments=appointment_data, salon=salon, **get_base_data())

# Admin update appointment status
@app.route('/admin/appointment/<int:appointment_id>/update', methods=['GET', 'POST'])
@login_required
def admin_update_appointment(appointment_id):
    if current_user.role not in ['admin', 'salon_owner']:
        abort(403)
    
    appointment = Appointment.query.get_or_404(appointment_id)
    
    # Verify ownership
    if current_user.role == 'salon_owner' and appointment.salon_id != current_user.salon_id:
        abort(403)
    
    form = AppointmentStatusForm()
    
    if form.validate_on_submit():
        appointment.status = form.status.data
        appointment.notes = form.notes.data
        
        db.session.commit()
        flash('Appointment status has been updated!', 'success')
        return redirect(url_for('admin_appointments'))
    
    elif request.method == 'GET':
        form.status.data = appointment.status
        form.notes.data = appointment.notes
    
    # Get related information
    user = User.query.get(appointment.user_id)
    service = Service.query.get(appointment.service_id)
    salon = Salon.query.get(appointment.salon_id)
    
    return render_template('admin/appointments.html', title='Update Appointment', 
                          form=form, appointment=appointment, user=user, 
                          service=service, salon=salon, edit_mode=True, **get_base_data())

# Error handlers
@app.errorhandler(404)
def page_not_found(e):
    return render_template('404.html', **get_base_data()), 404

@app.errorhandler(403)
def forbidden(e):
    return render_template('403.html', **get_base_data()), 403

@app.errorhandler(500)
def server_error(e):
    return render_template('500.html', **get_base_data()), 500
