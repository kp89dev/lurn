<?php
namespace App\Jobs\Infusionsoft;

use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Infusionsoft\Infusionsoft;

class PayInfusionsoftOrder implements InfusionsoftJobInterface
{
    use ResponseHandlerTrait;

    /**
     * @type Course
     */
    private $course;

    /**
     * @type \stdClass
     */
    private $card;

    /**
     * @type int
     */
    private $invoiceId;

    /**
     * PayInfusionsoftOrder constructor.
     *
     * @param Course    $course
     * @param \stdClass $card
     * @param int       $invoiceId
     */
    public function __construct(Course $course, \stdClass $card, int $invoiceId)
    {
        $this->course = $course;
        $this->card = $card;
        $this->invoiceId = $invoiceId;
    }

    public function handle()
    {
        /** @var $is \Infusionsoft\Infusionsoft */
        $is = app(Infusionsoft::class, ['account' => $this->course->infusionsoft->is_account]);

        try {
            $this->response = $is->invoices()->chargeInvoice(
                $this->invoiceId,
                'LurnCentral API Purchase',
                $this->card->Id,
                (int) $this->course->infusionsoft->getNextMerchantId(),
                false
            );

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleResponse()
    {
        if ($this->response['Successful'] === false) {
            $this->error = $this->response["Message"];
        }
    }
}
