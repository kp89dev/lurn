<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserSetting;
use App\Models\UserCertificate;
use App\Events\User\UserEmailChanged;
use App\Http\Requests\Account\SettingRequest;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Closure;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }

    public function index()
    {
        $refunds = user()->userRefunds;
        $onboarding = user()->getMission();
        $nextBonus = $onboarding->getNextBonus();
        $currentBonus = $onboarding->getCurrentBonus();
        $subscriptions = user()->courseSubscriptions()->with('course', 'course.infusionsoft')->get();

        return view('profile.settings', compact('onboarding', 'subscriptions', 'nextBonus', 'currentBonus', 'refunds'))
            ->withSettings(user()->setting ?? new UserSetting);
    }

    /**
     * @param SettingRequest $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(SettingRequest $request)
    {
        $user = user();

        if ($oldPassword = $request->password_old) {
            if (Hash::check($request->password_old, $user->password)) {
                $user->password = bcrypt($request->password_new);
            } else {
                return redirect()->back()
                    ->withErrors(['password_old' => 'Your old password is not correct.']);
            }
        }

        $user->setting()->updateOrCreate(['user_id' => $user->id], [
            'receive_updates' => (int) request('receive_updates', 0),
        ]);

        $this->storeProfilePicture();

        $user->fill($request->only(['name', 'email']));

        if ($user->isDirty(['email'])) {
            event(new UserEmailChanged($user));
        }

        $user->save();

        return redirect()->back()->with('success', 'Details saved successfully');
    }

    private function storeProfilePicture()
    {
        if ($image = request()->file('image')) {
            $path = $image->store('user', 'static');

            if ($path) {
                $this->deleteOldImage(user());

                $resized = Image::make(Storage::disk('static')->get($path))->fit(250, 250)->stream();
                Storage::disk('static')->put($path, (string) $resized);

                tap(user()->setting, function ($settings) use ($path) {
                    $settings->image = $path;
                })->save();
            }
        }
    }

    /**
     * @return certificate pdf
     */
    public function certificate()
    {
        $user = user();
        $cert = UserCertificate::where([['id', request('cert_id')], ['user_id', user()->id]])->first();
        $body = str_replace('$$USERNAME$$', $user->name, $cert->certificate_body);
        $data = [
            'certTitle'     => $cert->certificate_title,
            'certStyle'     => $cert->certificate_style,
            'body'          => $body,
            'crLogo'        => $cert->getSrc('logo'),
            'crLogoStyle'   => $cert->certificate_logo_style,
            'crBorder'      => $cert->getSrc('border'),
            'crBorderStyle' => $cert->certificate_border_style,
            'background'    => $cert->getSrc('background'),
            'crSign'        => $cert->getSrc('sign'),
            'crSignStyle'   => $cert->certificate_sign_style,
            'crBadge'       => $cert->getSrc('badge'),
            'crBadgeStyle'  => $cert->certificate_badge_style,
        ];

        if ($cert->certificate_date_bg) {
            $data['crDate'] = date('F jS, Y');
            $data['crDateBG'] = $cert->getSrc('date_bg');
            $data['crDateStyle'] = $cert->date_style;
            $pdf = Pdf::loadView('pages.classroom.certificate-dated', $data);
        }
        else {
            $pdf = PDF::loadView('pages.classroom.certificate', $data);
        }

        return $pdf->stream("{$cert->title}.pdf");
    }

    /**
     * @param Request $request
     *
     * @param Hasher  $hasher
     * @return bool
     */
    private function hasEnteredOldValidPassword(Request $request, Hasher $hasher)
    {
        return $hasher->check($request->password_old, user()->password);
    }

    /**
     * @param $user
     */
    private function deleteOldImage($user)
    {
        if ($user->setting->image) {
            Storage::disk('static')->delete($user->setting->image);
        }
    }
}
