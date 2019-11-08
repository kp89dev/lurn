<?php
/**
 * Date: 3/16/18
 * Time: 12:04 PM
 */

namespace App\Models\Queries;

use App\Commands\Base;
use App\Commands\Infusionsoft\Refunds;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\InfusionsoftContact;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserRefund;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class ProcessRefunds extends Base
{
    /** @var Refunds */
    protected $refundsHandler;

    /**
     * @var UserRefund
     */
    protected $userRefund;

    /**
     * @var InfusionsoftContact
     */
    private $infusionsoftContact;

    /**
     * @var CourseInfusionsoft
     */
    private $courseInfusionsoft;

    /**
     * @var UserCourse
     */
    private $userCourse;

    /**
     * @var DatabaseManager
     */
    private $db;

    /**
     * @var Command
     */
    private $command;

    /**
     * ProcessRefunds constructor.
     * @param UserRefund $userRefund
     * @param InfusionsoftContact $infusionsoftContact
     * @param CourseInfusionsoft $courseInfusionsoft
     * @param UserCourse $userCourse
     * @param DatabaseManager $db
     * @param Command $command
     */
    public function __construct(
        UserRefund $userRefund,
        InfusionsoftContact $infusionsoftContact,
        CourseInfusionsoft $courseInfusionsoft,
        UserCourse $userCourse,
        DatabaseManager $db,
        Command $command
    ) {
        $this->userRefund = $userRefund;
        $this->infusionsoftContact = $infusionsoftContact;
        $this->courseInfusionsoft = $courseInfusionsoft;
        $this->userCourse = $userCourse;
        $this->db = $db;
        $this->command = $command;
    }

    public function process()
    {
        $failed = false;

        try {
            $this->db->beginTransaction();
            $this->processRefunds();
        } catch (Exception $e) {
            $message = 'There was a problem processing the refunds to the database.';
            $this->db->rollBack();
            $this->refundsHandler->handleException($e, $message);
            $this->refundsHandler->failRefundTracker($message);
            $failed = true;
        }

        if (!$failed) {
            $this->db->commit();
            $this->refundsHandler->completeRefundTracker();
        }
    }

    protected function processRefunds()
    {
        foreach ($this->refundsHandler->getRefunds() as $refund) {
            if (!$this->hasRefundAlreadyBeenAdded($refund->get('Id'))) {
                $this->processRefund($refund);
            }
        }
    }

    /**
     * @param Collection $refund
     */
    protected function processRefund(Collection $refund)
    {
        $refundedAt = Carbon::parse($refund->get('LastUpdated'));
        $user = $this->getUser($refund);
        $course = $this->getCourse($refund);
        $userCourse = null;

        if ($refundedAt && $user && $course) {
            $this->saveUserCourse($user, $course, $refundedAt);
            $this->saveRefund($user, $course, $refundedAt, $refund->get('PayAmt'), $refund->get('Id'));
        }
    }

    /**
     * @param $isPaymentId
     * @return mixed
     */
    protected function hasRefundAlreadyBeenAdded($isPaymentId)
    {
        return $this->userRefund->where('is_payment_id', (int) $isPaymentId)->first();
    }

    /**
     * @param User $user
     * @param Course $course
     * @param Carbon $refundedAt
     */
    protected function saveUserCourse(User $user, Course $course, Carbon $refundedAt)
    {
        $userCourse = $this->getUserCourse($user, $course, $refundedAt);

        if ($userCourse) {
            $userCourse->update([
                'cancelled_at' => Carbon::now(),
                'refunded_at' => $refundedAt,
                'cancelled_reason' => 'Course was refunded',
                'status' => 1,
            ]);
        } else {
            UserCourse::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'cancelled_at' => Carbon::now(),
                'refunded_at' => $refundedAt,
                'created_at' => Carbon::now(),
                'cancelled_reason' => 'Course was refunded',
                'status' => 1,
            ]);
        }
    }

    /**
     * @param User $user
     * @param Course $course
     * @param Carbon $refundedAt
     * @param $amount
     */
    protected function saveRefund(User $user, Course $course, Carbon $refundedAt, $amount, $isPaymentId)
    {
        $this->userRefund->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $amount,
            'refunded_at' => $refundedAt,
            'is_payment_id' => $isPaymentId,
        ]);
    }

    /**
     * @param User $user
     * @param Course $course
     * @param Carbon $refundedAt
     * @return mixed
     */
    protected function getUserCourse(User $user, Course $course, Carbon $refundedAt)
    {
        return UserCourse::where([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'cancelled_at' => null
        ])->where(function ($query) use ($refundedAt) {
            $query->whereNull('refunded_at')
                ->orWhere('created_at', '<=', $refundedAt);
        })
        ->first();
    }

    /**
     * @param Collection $refund
     * @return User|null
     */
    protected function getUser(Collection $refund)
    {
        $infusionsoftContact = $this->getInfusionsoftContact($refund);

        return $infusionsoftContact ? $infusionsoftContact->user : null;
    }

    /**
     * @param Collection $refund
     * @return Course|null
     */
    protected function getCourse(Collection $refund)
    {
        $courseInfusionsoft = $this->getCourseInfusionsoft($refund);

        return $courseInfusionsoft ? $courseInfusionsoft->course : null;
    }

    /**
     * @param Collection $refund
     * @return InfusionsoftContact
     */
    protected function getInfusionsoftContact(Collection $refund)
    {
        return InfusionsoftContact::where('is_contact_id', (string)$refund->get('ContactId'))->first();
    }

    /**
     * @param Collection $refund
     * @return InfusionsoftContact
     */
    protected function getCourseInfusionsoft(Collection $refund)
    {
        $invoice = $refund->get('Invoice');
        return CourseInfusionsoft::where('is_product_id', (string)$invoice->get('ProductSold'))->first();
    }

    /**
     * @param Refunds $refundsHandler
     * @return ProcessRefunds
     */
    public function setRefundsHandler(Refunds $refundsHandler)
    {
        $this->refundsHandler = $refundsHandler;
        return $this;
    }
}