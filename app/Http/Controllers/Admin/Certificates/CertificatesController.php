<?php
namespace App\Http\Controllers\Admin\Certificates;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Course;
use App\Models\Certificate;
use App\Http\Requests\Admin\Certificates\StoreCertificateRequest;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\UserCertificate;

class CertificatesController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:courses,read')->only('index');
        $this->middleware('admin.role.auth:courses,write')->only('create', 'store', 'edit', 'update');
    }

    public function index(Course $course)
    {
        $certs = $course->certificates()->simplePaginate(20);

        return view('admin.certificates.index', compact('certs', 'course'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Course $course
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Course $course)
    {
        $cert = new Certificate();
        $action = route('certs.store', ['course' => $course]);
        $method = '';

        return view('admin.certificates.create-edit', compact('course', 'action', 'method', 'cert'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Course                  $course
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Course $course, Request $request)
    {
        $model = $course->certificates()->create($request->only('title'));
        $where = 'certs/' . $course->id;

        if (! empty($request->file())) {
            foreach ($request->file() as $name => $image) {
                $image = $image->store($where, 'static');
                $model->$name = $image;
            }
        }

        $model->style = $request->style;
        $model->body = $request->body;
        $model->logo_style = $request->logo_style;
        $model->border_style = $request->border_style;
        $model->sign_style = $request->sign_style;
        $model->badge_style = $request->badge_style;
        $model->date_style = $request->date_style;
        $model->save();

        return redirect()
                ->route('certs.index', ['course' => $course->id])
                ->with('alert-success', 'Certificate succesfully added');
    }

    /**
     * @param Course $course
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Course $course, Certificate $cert)
    {
        $action = route('certs.update', ['course' => $course, 'cert' => $cert]);
        $method = method_field('PUT');

        return view('admin.certificates.create-edit', compact('course', 'cert', 'action', 'method'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Course $course, Certificate $cert)
    {
        $cert->fill($request->only('title'));
        $where = 'certs/' . $course->id;
        
        if (! empty($request->file())) {
            \Illuminate\Support\Facades\Log::info($request->file());
            foreach ($request->file() as $name => $image) {
                $image = $image->store($where, 'static');
                if ($image) {
                    \Illuminate\Support\Facades\Log::info($image);
                    Storage::disk('static')->delete($cert->$name);
                    $cert->$name = $image;
                }
            }
        }

        $cert->style = $request->style;
        $cert->body = $request->body;
        $cert->logo_style = $request->logo_style;
        $cert->border_style = $request->border_style;
        $cert->sign_style = $request->sign_style;
        $cert->badge_style = $request->badge_style;
        $cert->date_style = $request->date_style;
        $cert->save();

        return redirect()->route('certs.index', ['course' => $course])
                ->with('alert-success', 'Certificate succesfully modified');
    }

    /**
     * @param Course      $course
     * @param Certificate $cert
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Course $course, Certificate $cert)
    {
        Storage::disk('static')->delete($cert->logo);
        Storage::disk('static')->delete($cert->border);
        Storage::disk('static')->delete($cert->background);
        Storage::disk('static')->delete($cert->sign);
        Storage::disk('static')->delete($cert->badge);
        Storage::disk('static')->delete($cert->date_bg);
        $cert->delete();

        return redirect()->route('certs.index', ['course' => $course])
                ->with('alert-success', 'Certificate succesfully deleted');
    }

    /**
     * @param Course      $course
     * @param Certificate $cert
     * @param             spec image $image
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function removeImage(Course $course, Certificate $cert, $image = '')
    {
        switch ($image) {
            case 'logo':
                Storage::disk('static')->delete($cert->logo);
                $cert->logo = '';
                Break;
            case 'border':
                Storage::disk('static')->delete($cert->border);
                $cert->border = '';
                Break;
            case 'background':
                Storage::disk('static')->delete($cert->background);
                $cert->background = '';
                Break;
            case 'sign':
                Storage::disk('static')->delete($cert->sign);
                $cert->sign = '';
                Break;
            case 'badge':
                Storage::disk('static')->delete($cert->badge);
                $cert->badge = '';
                Break;
            case 'date_bg':
                Storage::disk('static')->delete($cert->date_bg);
                $cert->date_bg = '';
                Break;
        }
        $cert->save();

        return redirect()->route('certs.edit', ['course' => $course, 'cert' => $cert])
                ->with('alert-success', 'Image succesfully deleted');
    }

    /**
     * @param Certificate $cert
     *
     * @throws \Exception
     */
    public function previewCert(Course $course, Certificate $cert)
    {
        $user = Auth::user();
        if ($user instanceof \App\Models\User){
            $body = str_replace('$$USERNAME$$',$user->name,$cert->body);
        }else{
            $body = $cert->body;
        }
        $data = [
            'certTitle' => $cert->title,
            'certStyle' => $cert->style,
            'body' => $body,
            'crLogo' => $cert->getSrc('logo'),
            'crLogoStyle' => $cert->logo_style,
            'crBorder' => $cert->getSrc('border'),
            'crBorderStyle' => $cert->border_style,
            'background' => $cert->getSrc('background'),
            'crSign' => $cert->getSrc('sign'),
            'crSignStyle' => $cert->sign_style,
            'crBadge' => $cert->getSrc('badge'),
            'crBadgeStyle' => $cert->badge_style
            ];

        if (!$cert->date_bg) {
            $pdf = Pdf::loadView('admin.certificates.preview', $data);
        } else {
            $data['crDate'] = date('F jS, Y');
            $data['crDateBG'] = $cert->getSrc('date_bg');
            $data['crDateStyle'] = $cert->date_style;
            $pdf = Pdf::loadView('admin.certificates.preview-dated', $data);
        }

        return $pdf->stream("{$cert->title}.pdf");
    }
    
    public function viewUserCert($userID, $certID)
    {
        $cert = UserCertificate::find($certID);
        
        $user = \App\Models\User::find($userID);
            
        $body = str_replace(
            '$$USERNAME$$',
            $user->name,
            $cert->certificate_body
        );

        $data = [
            'certTitle' => $cert->certificate_title,
            'certStyle' => $cert->certificate_style,
            'body' => $body,
            'crLogo' => $cert->getSrc('logo'),
            'crLogoStyle' => $cert->certificate_logo_style,
            'crBorder' => $cert->getSrc('border'),
            'crBorderStyle' => $cert->certificate_border_style,
            'background' => $cert->getSrc('background'),
            'crSign' => $cert->getSrc('sign'),
            'crSignStyle' => $cert->certificate_sign_style,
            'crBadge' => $cert->getSrc('badge'),
            'crBadgeStyle' => $cert->certificate_badge_style
            ];
        if (!$cert->certificate_date_bg){
            $pdf = Pdf::loadView('admin.certificates.preview', $data);
        }else {
            $data['crDate'] = date('F jS, Y');
            $data['crDateBG'] = $cert->getSrc('date_bg');
            $data['crDateStyle'] = $cert->date_style;
            $pdf = Pdf::loadView('admin.certificates.preview-dated', $data); 
        }

        return $pdf->stream("{$cert->certificate_title}.pdf");
    }
}
