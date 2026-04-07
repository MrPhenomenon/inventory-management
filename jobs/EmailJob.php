<?php

namespace app\jobs;

use Yii;
use app\models\Application;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class EmailJob extends BaseObject implements JobInterface
{
    public $application_id;
    public $template;
    public $subject;
    public $name;
    public $to_email;
    public $update_type;

    public function execute($queue)
    {
        $update_url = null;
        Yii::info("Starting EmailJob for Application ID: {$this->application_id}, template: {$this->template}, to: {$this->to_email}", 'app\jobs\EmailJob');

        try {
            $application = Application::findOne($this->application_id);

            if (is_null($application)) {
                Yii::error("Application {$this->application_id} not found, skipping email", 'app\jobs\EmailJob');
                return;
            }

            if ($this->update_type) {
                Yii::warning("Application {$this->application_id} requires update type {$this->update_type}", 'app\jobs\EmailJob');
                $payload = [
                    'id' => $application->id,
                    'type' => $this->update_type,
                    'expires' => time() + 3600 * 24 * 2,
                    'nonce' => bin2hex(random_bytes(8)),
                ];
                $token = Yii::$app->crypto->encrypt(json_encode($payload));
                $update_url = Yii::$app->urlManager->createAbsoluteUrl([
                    '/portal/user/update',
                    'token' => $token
                ]);
                Yii::$app->cache->set("used-token:$token", false, 3600 * 24 * 2);
            }

            $message = Yii::$app->mailer->compose(
                $this->template,
                [
                    'name' => $this->name,
                    'app_id' => $this->application_id,
                    'subject' => $this->subject,
                    'application' => $application,
                    'update_url' => $update_url,
                    'to' => $this->to_email
                ]
            )
                ->setFrom([Yii::$app->params['support_email'] => "EIN Applications Status"])
                ->setReplyTo([Yii::$app->params['reply_to_email'] => "EIN Applications Support"])
                ->setTo($this->to_email)
                ->setSubject($this->subject);

            switch ($this->template) {
                case "status-complete":
                    $files = $application->getFiles()->limit(3)->all();
                    foreach ($files as $file) {
                        if (!empty($file->file_url)) {
                            try {
                                Yii::info("Attaching file: {$file->file_url}", 'app\jobs\EmailJob');
                                $content = file_get_contents($file->file_url);
                                $filename = basename(parse_url($file->file_url, PHP_URL_PATH));
                                $message->attachContent($content, ['fileName' => $filename]);
                            } catch (\Exception $e) {
                                Yii::error("Failed to attach file {$file->file_url}: " . $e->getMessage(), 'app\jobs\EmailJob');
                            }
                        }
                    }
                    break;
                default:
                    Yii::info("No file attachments for template: {$this->template}", 'app\jobs\EmailJob');
                    break;
            }

            $sent = Yii::$app->mailer->send($message);

            if ($sent) {
                Yii::info("Email sent successfully to {$this->to_email} (Application ID: {$this->application_id})", 'app\jobs\EmailJob');
            } else {
                Yii::error("Mailer->send() returned false for {$this->to_email} (Application ID: {$this->application_id})", 'app\jobs\EmailJob');
            }
        } catch (\Throwable $e) {
            Yii::error("EmailJob crashed for Application ID {$this->application_id}: " . $e->getMessage() . "\n" . $e->getTraceAsString(), 'app\jobs\EmailJob');
            throw $e;
        }
    }
}
