@extends('dashboard.layouts.app')

@section('content')
    <section class="dashboard-card dashboard-panel">
        <div class="dashboard-panel__header">
            <div>
                <p class="dashboard-panel__eyebrow">Form Elements</p>
                <h3>Reusable Inputs</h3>
            </div>
        </div>

        <form class="dashboard-form">
            <div class="form-grid">
                <div class="form-field">
                    <label>Full Name</label>
                    <input type="text" placeholder="Admin user" />
                </div>
                <div class="form-field">
                    <label>Email Address</label>
                    <input type="email" placeholder="admin@example.com" />
                </div>
                <div class="form-field">
                    <label>Role</label>
                    <select>
                        <option>Administrator</option>
                        <option>Teacher</option>
                        <option>Student</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Plan</label>
                    <select>
                        <option>Enterprise</option>
                        <option>Professional</option>
                        <option>Starter</option>
                    </select>
                </div>
                <div class="form-field form-field--full">
                    <label>Notes</label>
                    <textarea rows="4" placeholder="Write internal notes or operational guidance..."></textarea>
                </div>
                <div class="form-field">
                    <label>Upload Attachment</label>
                    <input type="file" />
                </div>
                <div class="form-field">
                    <label>Notifications</label>
                    <div class="choice-group">
                        <label><input type="checkbox" checked> Email Alerts</label>
                        <label><input type="checkbox"> SMS Updates</label>
                    </div>
                </div>
                <div class="form-field form-field--full">
                    <label>Visibility</label>
                    <div class="choice-group choice-group--inline">
                        <label><input type="radio" name="visibility" checked> Public</label>
                        <label><input type="radio" name="visibility"> Private</label>
                        <label><input type="radio" name="visibility"> Internal</label>
                    </div>
                </div>
            </div>

            <div class="dashboard-form__actions">
                <button type="button" class="dashboard-button dashboard-button--ghost">Cancel</button>
                <button type="submit" class="dashboard-button">Submit Demo Form</button>
            </div>
        </form>
    </section>
@endsection
