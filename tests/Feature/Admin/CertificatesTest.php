<?php

namespace Tests\Feature\Admin;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Certificate;
use App\Models\UserCertificate;
use App\Models\Course;
use App\Models\User;

class CertificatesTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */

    public function certificates_index_is_available()
    {
        $course = factory(Course::class)->create();
        $response = $this->get(route('certs.index', ['course' => $course]));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function certificates_get_listed()
    {
        $course = factory(Course::class)->create();
        $cert = factory(Certificate::class)->create(['course_id' => $course->id]);

        $response = $this->get(route('certs.index', ['course' => $course]));

        $response->assertSee(htmlspecialchars($cert->title, ENT_QUOTES));
    }

    /**
     * @test
     */
    public function create_certificate_available()
    {
        $course = factory(Course::class)->create();

        $response = $this->get(route('certs.create', ['course' => $course]));

        $response->assertStatus(200)
            ->assertSee('Background')
            ->assertSee('Signature');
    }

    /**
     * @test
     */
    public function create_certificate_works()
    {
        $course = factory(Course::class)->create();
        $cert = factory(Certificate::class)->create(['course_id' => $course->id]);

        $filePath = base_path('tests/resources/img/file.jpg');
        $file = new UploadedFile($filePath, 'file.jpg', 'image/jpg', filesize($filePath), null, true);
        $response = $this->call(
            'POST',
            route('certs.store', $cert->toArray()),
            [],
            [],
            [
                'logo' => $file,
                'border' => $file,
                'background' => $file,
                'sign' => $file,
                'badge' => $file,
                'date_bg' => $file
            ]
        );

        $response->assertRedirect(route('certs.index', ['course' => $course]));

        $this->assertDatabaseHas('certificates', ['title' => $cert->title, 'course_id' => $course->id]);
    }

    /**
     * @test
     */
    public function edit_certificate_available()
    {
        $course = factory(Course::class)->create();
        $cert = factory(Certificate::class)->create(['course_id' => $course->id]);

        $response = $this->get(route('certs.edit', ['course' => $course, 'cert' => $cert]));

        $response->assertStatus(200)
            ->assertSee(htmlspecialchars($cert->title, ENT_QUOTES))
            ->assertSee('Save');
    }

    /**
     * @test
     */
    public function edit_certificate_works()
    {
        $course = factory(Course::class)->create();
        $cert = factory(Certificate::class)->create(['course_id' => $course->id]);
        $filePath = base_path('tests/resources/img/file.jpg');
        $file = new UploadedFile($filePath, 'file.jpg', 'image/jpg', filesize($filePath), null, true);
        $response = $this->call(
            'PUT',
            route('certs.update', ['course' => $course->id, 'cert' => $cert->id]),
            ['title' => 'TEST TITLE'],
            [],
            ['logo' => $file]
        );

        $response->assertRedirect(route('certs.index', ['course' => $course]));

        $this->assertDatabaseHas('certificates', ['title' => "TEST TITLE", 'id' => $cert->id]);
    }

    /**
     * @test
     */
    public function destroy_certificat_works()
    {
        $course = factory(Course::class)->create();
        $cert = factory(Certificate::class)->create(['course_id' => $course->id]);

        $response = $this->delete(route('certs.destroy', [
            'course_id' => $course->id,
            'cert' => $cert->id
        ]));

        $response->assertRedirect(route('certs.index', ['course' => $course]));

        $this->assertDatabaseMissing('certificates', ['id' => $cert->id]);
    }

    /**
     * Cancelled test because mPDF 7.0 that's used here uses headers_sent() to check if the headers have been sent
     * and trows an error. If you know a way to mock the process, please do.
     */
    public function check_cert_preview_works()
    {
        $this->markTestSkipped('Test needs to be reworked.');

        $course = factory(Course::class)->create();
        $cert = factory(Certificate::class)->create(['course_id' => $course->id]);

        ob_start();
        $response = $this->get(route('certs.previewCert', ['course' => $course, 'cert' => $cert]));
        ob_end_clean();
        $response->assertStatus(200);
    }

    /**
     * Cancelled test because mPDF 7.0 that's used here uses headers_sent() to check if the headers have been sent
     * and trows an error. If you know a way to mock the process, please do.
     */
    public function check_user_cert_view_works()
    {
        $cert = factory(UserCertificate::class)->create();

        ob_start();
        $response = $this->post(route('user.view.cert', ['userID' => $cert->user_id, 'certID' => $cert->id]));
        ob_end_clean();
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function check_cert_image_remove_works()
    {
        $course = factory(Course::class)->create();
        $cert = factory(Certificate::class)->create(['course_id' => $course->id]);

        $filePath = base_path('tests/resources/img/file.jpg');

        $file = new UploadedFile(
            $filePath,
            'file.jpg',
            'image/jpg',
            filesize($filePath),
            null,
            true
        );
        $response = $this->call(
            'POST',
            route('certs.store', $cert->toArray()),
            [],
            [],
            [
                'logo' => $file,
                'border' => $file,
                'background' => $file,
                'sign' => $file,
                'badge' => $file,
                'date_bg' => $file
            ]
        );

        $response = $this->get(route(
            'certs.removeImage',
            ['course' => $course, 'cert' => $cert, 'image' => 'background']
        ));
        $response->assertRedirect(route('certs.edit', ['course' => $course, 'cert' => $cert]));

        $response = $this->get(route(
            'certs.removeImage',
            ['course' => $course, 'cert' => $cert, 'image' => 'logo']
        ));
        $response->assertRedirect(route('certs.edit', ['course' => $course, 'cert' => $cert]));

        $response = $this->get(route(
            'certs.removeImage',
            ['course' => $course, 'cert' => $cert, 'image' => 'border']
        ));
        $response->assertRedirect(route('certs.edit', ['course' => $course, 'cert' => $cert]));

        $response = $this->get(route(
            'certs.removeImage',
            ['course' => $course, 'cert' => $cert, 'image' => 'date_bg']
        ));
        $response->assertRedirect(route('certs.edit', ['course' => $course, 'cert' => $cert]));

        $response = $this->get(route(
            'certs.removeImage',
            ['course' => $course, 'cert' => $cert, 'image' => 'badge']
        ));
        $response->assertRedirect(route('certs.edit', ['course' => $course, 'cert' => $cert]));

        $response = $this->get(route(
            'certs.removeImage',
            ['course' => $course, 'cert' => $cert, 'image' => 'sign']
        ));
        $response->assertRedirect(route('certs.edit', ['course' => $course, 'cert' => $cert]));

        $response = $this->get(route('certs.edit', ['course' => $course, 'cert' => $cert]));
        $response
            ->assertDontSee(route(
                'certs.removeImage',
                ['course' => $course, 'cert' => $cert, 'image' => 'background']
            ))
            ->assertDontSee(route(
                'certs.removeImage',
                ['course' => $course, 'cert' => $cert, 'image' => 'logo']
            ))
            ->assertDontSee(route(
                'certs.removeImage',
                ['course' => $course, 'cert' => $cert, 'image' => 'border']
            ))
            ->assertDontSee(route(
                'certs.removeImage',
                ['course' => $course, 'cert' => $cert, 'image' => 'date_bg']
            ))
            ->assertDontSee(route(
                'certs.removeImage',
                ['course' => $course, 'cert' => $cert, 'image' => 'badge']
            ))
            ->assertDontSee(route(
                'certs.removeImage',
                ['course' => $course, 'cert' => $cert, 'image' => 'sign']
            ));
    }

}
