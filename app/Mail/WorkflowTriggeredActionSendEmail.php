<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkflowTriggeredActionSendEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @type string
     */
    private $subjectLine;

    /**
     * @type string
     */
    private $content;

    public function __construct(string $subjectLine, string $content)
    {
        $this->subjectLine = $subjectLine;
        $this->content = $content;
    }

    /**
     * @codeCoverageIgnore
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => config('support.email'), 'name' => config('support.name')])
                    ->subject($this->subjectLine)
                    ->markdown('admin.templates.var.responsive')
                    ->with([
                        'content' => $this->content,
                        'triggered' => 'workflow'
                        ]);
    }
}
