<?php

/**
 * @link      http://github.com/zetta-repo/zend-bootstrap for the canonical source repository
 * @copyright Copyright (c) 2017 Zetta Code
 */

namespace Zetta\ZendBootstrap\Controller\Plugin;

use Laminas\Mail\Message;
use Laminas\Mime;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

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
    protected $encoding = 'UTF-8';

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

    function sendEmail($to, $subject, $html, $text = null, $attachments = [], $replyTo = false)
    {
        // HTML part
        $htmlPart = new Mime\Part($html);
        $htmlPart->setType(Mime\Mime::TYPE_HTML)
            ->setCharset($this->encoding)
            ->setEncoding(Mime\Mime::ENCODING_QUOTEDPRINTABLE);
        if ($text !== null) {
            // Plain text part
            $textPart = new Mime\Part($text);
            $textPart->setType(Mime\Mime::TYPE_TEXT)
                ->setCharset($this->encoding)
                ->setEncoding(Mime\Mime::ENCODING_QUOTEDPRINTABLE);
        } else {
            $textPart = null;
        }

        $body = new Mime\Message();
        if (count($attachments) > 0) {
            // With attachments, we need a multipart/related email. First part
            // is itself a multipart/alternative message
            $content = new Mime\Message();
            if ($text !== null) {
                $content->addPart($textPart);
            }
            $content->addPart($htmlPart);

            $contentPart = new Mime\Part($content->generateMessage());
            $contentPart->setType(Mime\Mime::MULTIPART_ALTERNATIVE)
                ->setBoundary($content->getMime()->boundary());

            $body->addPart($contentPart);
            $messageType = Mime\Mime::MULTIPART_RELATED;

            // Add each attachment
            foreach ($attachments as $thisAttachment) {
                $attachment = new Mime\Part($thisAttachment['content']);
                $attachment->filename = $thisAttachment['filename'];
                $attachment->setType(Mime\Mime::TYPE_OCTETSTREAM)
                    ->setEncoding(Mime\Mime::ENCODING_BASE64)
                    ->setDisposition(Mime\Mime::DISPOSITION_ATTACHMENT);

                $body->addPart($attachment);
            }
        } else {
            // No attachments, just add the two textual parts to the body
            if ($text !== null) {
                $body->addPart($textPart);
                $messageType = Mime\Mime::MULTIPART_ALTERNATIVE;
            } else {
                $messageType = null;
            }
            $body->addPart($htmlPart);
        }

        // attach the body to the message and set the content-type
        $message = new Message();
        $message->addTo($to);
        if ($replyTo) {
            $message->addReplyTo($replyTo);
        }
        $message->setEncoding($this->encoding)
            ->addFrom($this->fromEmail, $this->fromName)
            ->setSubject($subject)
            ->setBody($body);
        if ($text !== null) {
            $message->getHeaders()->get('content-type')->setType($messageType);
        }

        $this->getTransport()->send($message);
    }
}
