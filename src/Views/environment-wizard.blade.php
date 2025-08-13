@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.environment.wizard.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-magic fa-fw" aria-hidden="true"></i>
    {!! trans('installer_messages.environment.wizard.title') !!}
@endsection

@section('style')
<style>
* { box-sizing: border-box; }

.form-container {
    max-height: 75vh;
    overflow-y: auto;
    padding: 20px;
    margin-bottom: 15px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e1e5e9;
}

.form-container::-webkit-scrollbar { width: 6px; }
.form-container::-webkit-scrollbar-track { background: #f5f5f5; border-radius: 3px; }
.form-container::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
.form-container::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

.form-section {
    margin: 0;
    padding: 12px;
    border-bottom: none;
    background: #fff;
}
.form-section:first-child { border-top-left-radius: 8px; border-top-right-radius: 8px; }
.form-section:last-child { border-bottom: none; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }

.section-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    padding-bottom: 0;
}
.section-title i { margin-right: 10px; color: #5a6c7d; font-size: 18px; }

.form-group { margin-bottom: 8px; position: relative; }
.form-group:last-child { margin-bottom: 0; }

.form-group label {
    display: block;
    margin-bottom: 3px;
    font-weight: 500;
    color: #374151;
    font-size: 13px;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    background: #fff;
    color: #374151;
    height: 32px;
    font-family: inherit;
}

.form-group input::placeholder { color: #9ca3af; }

.form-group select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 14px;
    padding-right: 35px;
    cursor: pointer;
}

.form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: #2563eb;
}

.form-group.has-error input, .form-group.has-error select { border-color: #dc2626; }

.error-block {
    color: #dc2626;
    font-size: 12px;
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
}

#environment_text_input {
    margin-top: 6px;
    padding: 8px;
    background: #f9fafb;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.buttons { text-align: center; margin-top: 10px; padding: 8px; }

.button {
    background: #34a0db;
    color: white;
    border: none;
    padding: 12px 28px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    min-width: 140px;
}
.button:hover:not(:disabled) { background: #2490cb; }
.button:disabled { opacity: 0.6; cursor: not-allowed; background: #9ca3af; }

.button-text { display: flex; align-items: center; justify-content: center; gap: 8px; }
.loading-spinner { display: none; align-items: center; justify-content: center; gap: 8px; }
.loading-spinner i { animation: spin 1s linear infinite; color: #fff; font-size: 14px; }

@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

@media (max-width: 768px) {
    .form-container { max-height: 70vh; margin: 8px; padding: 10px; }
    .form-section { padding: 10px; }
    .section-title { font-size: 15px; margin-bottom: 6px; }
    .button { width: 100%; padding: 10px 20px; min-width: auto; }
    .form-group input, .form-group select { padding: 6px 10px; height: 30px; }
    .form-group { margin-bottom: 6px; }
    .buttons { margin-top: 8px; padding: 6px; }
}
</style>
@endsection

@section('container')
<form method="post" action="{{ route('LaravelInstaller::environmentSaveWizard') }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="app_url" id="app_url" value="">
    
    <div class="form-container">
        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-cog fa-fw" aria-hidden="true"></i>
                {{ trans('installer_messages.environment.wizard.tabs.environment') }}
            </div>
            
            <div class="form-group {{ $errors->has('app_name') ? ' has-error ' : '' }}">
                <label for="app_name">{{ trans('installer_messages.environment.wizard.form.app_name_label') }}</label>
                <input type="text" name="app_name" id="app_name" value="{{ old('app_name') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_name_placeholder') }}" />
                @if ($errors->has('app_name'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('app_name') }}
                    </span>
                @endif
            </div>
            
            <div class="form-group {{ $errors->has('environment') ? ' has-error ' : '' }}">
                <label for="environment">{{ trans('installer_messages.environment.wizard.form.app_environment_label') }}</label>
                <select name="environment" id="environment" onchange='checkEnvironment(this.value);'>
                    <option value="local">{{ trans('installer_messages.environment.wizard.form.app_environment_label_local') }}</option>
                    <option value="production" selected>{{ trans('installer_messages.environment.wizard.form.app_environment_label_production') }}</option>
                </select>
                <div id="environment_text_input" style="display: none;">
                    <input type="text" name="environment_custom" id="environment_custom" placeholder="{{ trans('installer_messages.environment.wizard.form.app_environment_placeholder_other') }}"/>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-database fa-fw" aria-hidden="true"></i>
                {{ trans('installer_messages.environment.wizard.tabs.database') }}
            </div>
            
            <div class="form-group {{ $errors->has('database_connection') ? ' has-error ' : '' }}">
                <label for="database_connection">{{ trans('installer_messages.environment.wizard.form.db_connection_label') }}</label>
                <select name="database_connection" id="database_connection" onchange="checkDatabaseConnection(this.value);">
                    <option value="mysql" selected>{{ trans('installer_messages.environment.wizard.form.db_connection_label_mysql') }}</option>
                    <option value="sqlite">{{ trans('installer_messages.environment.wizard.form.db_connection_label_sqlite') }}</option>
                    <option value="pgsql">{{ trans('installer_messages.environment.wizard.form.db_connection_label_pgsql') }}</option>
                    <option value="sqlsrv">{{ trans('installer_messages.environment.wizard.form.db_connection_label_sqlsrv') }}</option>
                </select>
                @if ($errors->has('database_connection'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('database_connection') }}
                    </span>
                @endif
            </div>
            
            <div id="mysql_fields" class="database-fields">
                <div class="form-group {{ $errors->has('database_hostname') ? ' has-error ' : '' }}">
                    <label for="database_hostname">{{ trans('installer_messages.environment.wizard.form.db_host_label') }}</label>
                    <input type="text" name="database_hostname" id="database_hostname" value="127.0.0.1" placeholder="{{ trans('installer_messages.environment.wizard.form.db_host_placeholder') }}" />
                    @if ($errors->has('database_hostname'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('database_hostname') }}
                        </span>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('database_port') ? ' has-error ' : '' }}">
                    <label for="database_port">{{ trans('installer_messages.environment.wizard.form.db_port_label') }}</label>
                    <input type="number" name="database_port" id="database_port" value="3306" placeholder="{{ trans('installer_messages.environment.wizard.form.db_port_placeholder') }}" />
                    @if ($errors->has('database_port'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('database_port') }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="form-group {{ $errors->has('database_name') ? ' has-error ' : '' }}">
                <label for="database_name">{{ trans('installer_messages.environment.wizard.form.db_name_label') }}</label>
                <input type="text" name="database_name" id="database_name" value="{{ old('database_name') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.db_name_placeholder') }}" />
                @if ($errors->has('database_name'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('database_name') }}
                    </span>
                @endif
            </div>
            
            <div class="form-group {{ $errors->has('database_username') ? ' has-error ' : '' }}">
                <label for="database_username">{{ trans('installer_messages.environment.wizard.form.db_username_label') }}</label>
                <input type="text" name="database_username" id="database_username" value="{{ old('database_username') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.db_username_placeholder') }}" />
                @if ($errors->has('database_username'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('database_username') }}
                    </span>
                @endif
            </div>
            
            <div class="form-group {{ $errors->has('database_password') ? ' has-error ' : '' }}">
                <label for="database_password">{{ trans('installer_messages.environment.wizard.form.db_password_label') }}</label>
                <input type="password" name="database_password" id="database_password" value="" placeholder="{{ trans('installer_messages.environment.wizard.form.db_password_placeholder') }}" />
                @if ($errors->has('database_password'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('database_password') }}
                    </span>
                @endif
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-title">
                <i class="fa fa-cogs fa-fw" aria-hidden="true"></i>
                {{ trans('installer_messages.environment.wizard.tabs.application') }}
            </div>
            
            <div class="form-group {{ $errors->has('admin_name') ? ' has-error ' : '' }}">
                <label for="admin_name">{{ trans('installer_messages.environment.wizard.form.admin_name_label') }}</label>
                <input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.admin_name_placeholder') }}" />
                @if ($errors->has('admin_name'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('admin_name') }}
                    </span>
                @endif
            </div>

            <div class="form-group {{ $errors->has('admin_email') ? ' has-error ' : '' }}">
                <label for="admin_email">{{ trans('installer_messages.environment.wizard.form.admin_email_label') }}</label>
                <input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.admin_email_placeholder') }}" />
                @if ($errors->has('admin_email'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('admin_email') }}
                    </span>
                @endif
            </div>

            <div class="form-group {{ $errors->has('admin_password') ? ' has-error ' : '' }}">
                <label for="admin_password">{{ trans('installer_messages.environment.wizard.form.admin_password_label') }}</label>
                <input type="password" name="admin_password" id="admin_password" value="{{ old('admin_password') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.admin_password_placeholder') }}" />
                @if ($errors->has('admin_password'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('admin_password') }}
                    </span>
                @endif
            </div>

            <div class="form-group {{ $errors->has('admin_password_confirmation') ? ' has-error ' : '' }}">
                <label for="admin_password_confirmation">{{ trans('installer_messages.environment.wizard.form.admin_password_confirm_label') }}</label>
                <input type="password" name="admin_password_confirmation" id="admin_password_confirmation" value="{{ old('admin_password_confirmation') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.admin_password_confirm_placeholder') }}" />
                @if ($errors->has('admin_password_confirmation'))
                    <span class="error-block">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ $errors->first('admin_password_confirmation') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="buttons">
        <button class="button" type="submit" id="submitButton">
            <span class="button-text">
                {{ trans('installer_messages.environment.wizard.form.buttons.install') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </span>
            <span class="loading-spinner">
                <i class="fa fa-spinner fa-spin"></i> Installing...
            </span>
        </button>
    </div>
</form>
@endsection

@section('scripts')
    <script type="text/javascript">
        function getCurrentURL() {
            return window.location.protocol + "//" + window.location.host;
        }

        function showLoading() {
            const button = document.getElementById('submitButton');
            const buttonText = button.querySelector('.button-text');
            const loadingSpinner = button.querySelector('.loading-spinner');
            
            buttonText.style.display = 'none';
            loadingSpinner.style.display = 'flex';
            button.disabled = true;
        }

        function checkEnvironment(val) {
            const element = document.getElementById('environment_text_input');
            element.style.display = val === 'other' ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('app_url').value = getCurrentURL();
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            document.getElementById('app_url').value = getCurrentURL();
            showLoading();
        });
</script>
@endsection