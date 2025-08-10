@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.environment.wizard.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-magic fa-fw" aria-hidden="true"></i>
    {!! trans('installer_messages.environment.wizard.title') !!}
@endsection

@section('styles')
<style>
    .button-text {
        display: inline-block;
    }
    .loading-spinner {
        display: none;
    }
    .button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Scrollbar styles */
    .form-container {
        max-height: 70vh;
        overflow-y: auto;
        padding-right: 15px;
        margin-bottom: 20px;
    }
    
    /* Custom scrollbar styling */
    .form-container::-webkit-scrollbar {
        width: 10px;
    }
    
    .form-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }
    
    .form-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }
    
    .form-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Section styling */
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #333;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 10px;
        color: #666;
    }
</style>
@endsection

@section('container')
    <form method="post" action="{{ route('LaravelInstaller::environmentSaveWizard') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="app_url" id="app_url" value="">
        
        <div class="form-container">
            <!-- Environment Settings Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-cog fa-fw" aria-hidden="true"></i>
                    {{ trans('installer_messages.environment.wizard.tabs.environment') }}
                </div>
                
                <div class="form-group {{ $errors->has('app_name') ? ' has-error ' : '' }}">
                    <label for="app_name">
                        {{ trans('installer_messages.environment.wizard.form.app_name_label') }}
                    </label>
                    <input type="text" name="app_name" id="app_name" value="{{ old('app_name') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.app_name_placeholder') }}" />
                    @if ($errors->has('app_name'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('app_name') }}
                        </span>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('environment') ? ' has-error ' : '' }}">
                    <label for="environment">
                        {{ trans('installer_messages.environment.wizard.form.app_environment_label') }}
                    </label>
                    <select name="environment" id="environment" onchange='checkEnvironment(this.value);'>
                        <option value="local">{{ trans('installer_messages.environment.wizard.form.app_environment_label_local') }}</option>
                        <option value="production" selected>{{ trans('installer_messages.environment.wizard.form.app_environment_label_production') }}</option>
                    </select>
                    <div id="environment_text_input" style="display: none;">
                        <input type="text" name="environment_custom" id="environment_custom" placeholder="{{ trans('installer_messages.environment.wizard.form.app_environment_placeholder_other') }}"/>
                    </div>
                </div>
            </div>
            
            <!-- Database Settings Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-database fa-fw" aria-hidden="true"></i>
                    {{ trans('installer_messages.environment.wizard.tabs.database') }}
                </div>
                
                <div class="form-group {{ $errors->has('database_connection') ? ' has-error ' : '' }}">
                    <label for="database_connection">
                        {{ trans('installer_messages.environment.wizard.form.db_connection_label') }}
                    </label>
                    <select name="database_connection" id="database_connection">
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
                
                <div class="form-group {{ $errors->has('database_hostname') ? ' has-error ' : '' }}">
                    <label for="database_hostname">
                        {{ trans('installer_messages.environment.wizard.form.db_host_label') }}
                    </label>
                    <input type="text" name="database_hostname" id="database_hostname" value="127.0.0.1" placeholder="{{ trans('installer_messages.environment.wizard.form.db_host_placeholder') }}" />
                    @if ($errors->has('database_hostname'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('database_hostname') }}
                        </span>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('database_port') ? ' has-error ' : '' }}">
                    <label for="database_port">
                        {{ trans('installer_messages.environment.wizard.form.db_port_label') }}
                    </label>
                    <input type="number" name="database_port" id="database_port" value="3306" placeholder="{{ trans('installer_messages.environment.wizard.form.db_port_placeholder') }}" />
                    @if ($errors->has('database_port'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('database_port') }}
                        </span>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('database_name') ? ' has-error ' : '' }}">
                    <label for="database_name">
                        {{ trans('installer_messages.environment.wizard.form.db_name_label') }}
                    </label>
                    <input type="text" name="database_name" id="database_name" value="{{ old('database_name') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.db_name_placeholder') }}" />
                    @if ($errors->has('database_name'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('database_name') }}
                        </span>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('database_username') ? ' has-error ' : '' }}">
                    <label for="database_username">
                        {{ trans('installer_messages.environment.wizard.form.db_username_label') }}
                    </label>
                    <input type="text" name="database_username" id="database_username" value="{{ old('database_username') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.db_username_placeholder') }}" />
                    @if ($errors->has('database_username'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('database_username') }}
                        </span>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('database_password') ? ' has-error ' : '' }}">
                    <label for="database_password">
                        {{ trans('installer_messages.environment.wizard.form.db_password_label') }}
                    </label>
                    <input type="password" name="database_password" id="database_password" value="" placeholder="{{ trans('installer_messages.environment.wizard.form.db_password_placeholder') }}" />
                    @if ($errors->has('database_password'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('database_password') }}
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Application Settings Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-cogs fa-fw" aria-hidden="true"></i>
                    {{ trans('installer_messages.environment.wizard.tabs.application') }}
                </div>
                
                <div class="form-group {{ $errors->has('admin_name') ? ' has-error ' : '' }}">
                    <label for="admin_name">
                        {{ trans('installer_messages.environment.wizard.form.admin_name_label') }}
                    </label>
                    <input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.admin_name_placeholder') }}" />
                    @if ($errors->has('admin_name'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('admin_name') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('admin_email') ? ' has-error ' : '' }}">
                    <label for="admin_email">
                        {{ trans('installer_messages.environment.wizard.form.admin_email_label') }}
                    </label>
                    <input type="text" name="admin_email" id="admin_email" value="{{ old('admin_email') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.admin_email_placeholder') }}" />
                    @if ($errors->has('admin_email'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('admin_email') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('admin_password') ? ' has-error ' : '' }}">
                    <label for="admin_password">
                        {{ trans('installer_messages.environment.wizard.form.admin_password_label') }}
                    </label>
                    <input type="password" name="admin_password" id="admin_password" value="{{ old('admin_password') }}" placeholder="{{ trans('installer_messages.environment.wizard.form.admin_password_placeholder') }}" />
                    @if ($errors->has('admin_password'))
                        <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('admin_password') }}
                        </span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('admin_password_confirmation') ? ' has-error ' : '' }}">
                    <label for="admin_password_confirmation">
                        {{ trans('installer_messages.environment.wizard.form.admin_password_confirm_label') }}
                    </label>
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
            <button class="button" type="submit" id="submitButton" onclick="this.disabled=true;showLoading();this.form.submit();return false;">
                <span class="button-text">
                    {{ trans('installer_messages.environment.wizard.form.buttons.install') }}
                    <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                </span>
                <span class="loading-spinner" style="display: none;">
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
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('app_url').value = getCurrentURL();
        });
        document.querySelector('form').addEventListener('submit', function(e) {
            document.getElementById('app_url').value = getCurrentURL();
        });
        function showLoading() {
            const button = document.getElementById('submitButton');
            const buttonText = button.querySelector('.button-text');
            const loadingSpinner = button.querySelector('.loading-spinner');
            
            buttonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block';
            button.disabled = true;
        }
        function checkEnvironment(val) {
            var element=document.getElementById('environment_text_input');
            if(val=='other') {
                element.style.display='block';
            } else {
                element.style.display='none';
            }
        }
    </script>
@endsection