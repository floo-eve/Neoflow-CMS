<?php

namespace Neoflow\CMS\Service;

use Neoflow\Framework\Core\AbstractService;
use Neoflow\Support\Mailer\Mail;

class MailService extends AbstractService
{

    /**
     * Create mail
     *
     * @return Mail
     */
    public function create($to, $subject = '', $message = '')
    {
        $from = $this->app()
            ->get('config')
            ->get('email');

        $mail = new Mail();
        return $mail
                ->setFrom($from)
                ->addTo($to)
                ->setSubject($subject)
                ->setMessage($message);
    }
}
