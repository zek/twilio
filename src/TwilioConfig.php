<?php
namespace NotificationChannels\Twilio;

class TwilioConfig
{
    /**
     * @var array
     */
    private $config;

    /**
     * TwilioConfig constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getAccountSid()
    {
        return $this->config['account_sid'];
    }

    public function getAuthToken()
    {
        return $this->config['auth_token'];
    }

    /**
     * Get the from entity from config
     */
    public function getFrom()
    {
        return $this->config['from'];
    }

    public function getAlphanumericSender()
    {
        if (isset($this->config['alphanumeric_sender'])) {
            return $this->config['alphanumeric_sender'];
        }

        return null;
    }

    public function getSmsParams()
    {
        $params = [];

        if (isset($this->config['sms_service_sid'])) {
            $params['MessagingServiceSid'] = $this->config['sms_service_sid'];
        }

        return $params;
    }
}