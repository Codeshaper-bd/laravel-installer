<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (! file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function saveFileWizard(Request $request)
    {
        $results = trans('installer_messages.environment.success');

        $envVariableMap = [
            'app_name'            => 'APP_NAME',
            'environment'         => 'APP_ENV',
            'app_url'             => 'APP_URL',
            'database_connection' => 'DB_CONNECTION',
            'database_hostname'   => 'DB_HOST',
            'database_port'       => 'DB_PORT',
            'database_name'       => 'DB_DATABASE',
            'database_username'   => 'DB_USERNAME',
            'database_password'   => 'DB_PASSWORD',
            'central_domain'      => 'CENTRAL_DOMAIN',
            'tenant_db_prefix'    => 'TENANT_DB_PREFIX',
        ];

        try {
            $envPath = base_path('.env');
            $envLines = file($envPath, FILE_IGNORE_NEW_LINES);
            
            // Process special fields before the main loop
            $centralDomain = '';
            if ($request->has('central_domain')) {
                $centralDomain = $this->extractDomain($request->input('central_domain'));
            }
            
            $tenantDbPrefix = '';
            if ($request->has('database_name')) {
                $dbName = $request->input('database_name');
                $tenantDbPrefix = $dbName . '_';
            }

            foreach ($envLines as &$line) {
                // Skip empty or commented lines
                if (trim($line) === '' || str_starts_with(trim($line), '#')) {
                    continue;
                }

                foreach ($envVariableMap as $formField => $envKey) {
                    if ($formField === 'central_domain' && str_starts_with($line, $envKey . '=')) {
                        // Use the extracted domain value
                        $line = $envKey . '=' . $this->sanitizeEnvValue($centralDomain);
                    } else if ($formField === 'tenant_db_prefix' && str_starts_with($line, $envKey . '=')) {
                        // Use the generated tenant DB prefix
                        $line = $envKey . '=' . $this->sanitizeEnvValue($tenantDbPrefix);
                    } else if ($request->has($formField) && str_starts_with($line, $envKey . '=')) {
                        $newValue = $this->sanitizeEnvValue($request->input($formField));
                        $line = $envKey . '=' . $newValue;
                    }
                }
            }

            file_put_contents($envPath, implode("\n", $envLines));
        } catch (Exception $e) {
            $results = trans('installer_messages.environment.errors');
        }

        return $results;
    }

    protected function sanitizeEnvValue($value)
    {
        // Wrap in single quotes if it contains spaces or special characters
        if (preg_match('/\s|["\'#]/', $value)) {
            return "'" . addslashes($value) . "'";
        }

        return $value;
    }
    
    /**
     * Extract domain from URL by removing http/https protocol, trailing slash, and www.
     *
     * @param string $url
     * @return string
     */
    protected function extractDomain($url)
    {
        // Remove http:// or https:// from the URL
        $domain = str_replace(['http://', 'https://'], '', $url);
        $domain = rtrim($domain, '/');
        $domain = str_replace('www.', '', $domain);
        
        return $domain;
    }
}
