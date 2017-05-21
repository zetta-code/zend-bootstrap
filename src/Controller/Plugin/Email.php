<?php
/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2016 Zetta Code
 */

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Zend\Mail\Message;
use Zend\Mime;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Email extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @var string
     */
    protected $encoding = 'utf8';

    /**
     * @var AbstractController
     */
    protected $controller;

    protected $transport;

    /**
     * EmailPlugin constructor.
     * @param $transport
     * @param array $config
     */
    public function __construct($transport, $config = [])
    {
        $this->controller = $this->getController();
        $this->transport = $transport;
        $this->fromEmail = $config['from-email'];
        $this->fromName = $config['from-name'];
    }

    public function send($to, $subject, $body)
    {
        $message = new Message();
        $message->addTo($to);
        $message->setEncoding($this->encoding);
        $message->addFrom($this->fromEmail, $this->fromName);
        $message->setSubject($subject);
        $message->setBody($body);

        $this->getTransport()->send($message);
    }

    /**
     * @return mixed
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param mixed $transport
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    function sendEmail($to, $subject, $html, $text, $attachments = null)
    {
        // HTML part
        $htmlPart = new Mime\Part($html);
        $htmlPart->setEncoding(Mime\Mime::ENCODING_QUOTEDPRINTABLE);
        $htmlPart->setType(Mime\Mime::TYPE_HTML);
        // Plain text part
        $textPart = new Mime\Part($text);
        $textPart->setEncoding(Mime\Mime::ENCODING_QUOTEDPRINTABLE);
        $textPart->setType(Mime\Mime::TYPE_TEXT);

        $body = new Mime\Message();
        if ($attachments) {
            // With attachments, we need a multipart/related email. First part
            // is itself a multipart/alternative message
            $content = new Mime\Message();
            $content->addPart($textPart);
            $content->addPart($htmlPart);

            $contentPart = new Mime\Part($content->generateMessage());
            $contentPart->setType(Mime\Mime::MULTIPART_ALTERNATIVE);
            $contentPart->setBoundary($content->getMime()->boundary());

            $body->addPart($contentPart);
            $messageType = Mime\Mime::MULTIPART_RELATED;

            // Add each attachment
            foreach ($attachments as $thisAttachment) {
                $attachment = new Mime\Part($thisAttachment['content']);
                $attachment->filename = $thisAttachment['filename'];
                $attachment->type = Mime\Mime::TYPE_OCTETSTREAM;
                $attachment->encoding = Mime\Mime::ENCODING_BASE64;
                $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;

                $body->addPart($attachment);
            }

        } else {
            // No attachments, just add the two textual parts to the body
            $body->setParts([$textPart, $htmlPart]);
            $messageType = Mime\Mime::MULTIPART_ALTERNATIVE;
        }

        // attach the body to the message and set the content-type
        $message = new Message();
        $message->addTo($to);
        $message->setEncoding($this->encoding);
        $message->addFrom($this->fromEmail, $this->fromName);
        $message->setSubject($subject);
        $message->setBody($body);
        $message->getHeaders()->get('content-type')->setType($messageType);

        $this->getTransport()->send($message);
    }
}
