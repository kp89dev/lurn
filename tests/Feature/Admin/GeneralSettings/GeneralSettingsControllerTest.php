<?php
namespace Feature\Admin\GeneralSettings;

use App\Models\InfusionsoftMerchantId;

class GeneralSettingsControllerTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function no_id_is_defined()
    {
        $response = $this->get(route('view.settings'));
        $response->assertStatus(200);

        $accounts = explode(',', env('IS_ACCOUNTS'));
        foreach ($accounts as $account) {
            $response->assertSee($account);
        }
    }

    /**
     * @test
     */
    public function defined_ids_are_shown()
    {
        factory(InfusionsoftMerchantId::class)->create([
            'account' => 'uf233',
            'ids' => [33,44, 55]
        ]);

        $response = $this->get(route('view.settings'));
        $response->assertStatus(200);

        $response->assertSee('33');
        $response->assertSee('44');
        $response->assertSee('55');
    }

    /**
     * @test
     */
    public function store_saves_new_ids()
    {
        $response = $this->post(route('store.settings'),[
            'id_uf233' => [44, 55, 66]
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('infusionsoft_merchant_ids', [
            'account' => 'uf233',
            'ids' => json_encode([44, 55, 66])
        ]);
    }

    /**
     * @test
     */
    public function store_removes_zeros()
    {
        $response = $this->post(route('store.settings'),[
            'id_uf233' => [44, 0, 66]
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('infusionsoft_merchant_ids', [
            'account' => 'uf233',
            'ids' => json_encode([44, 66])
        ]);
    }

    /**
     * @test
     */
    public function edit_modifies_existing_values()
    {
        factory(InfusionsoftMerchantId::class)->create([
            'account' => 'uf233',
            'ids' => [33,44,55]
        ]);

        $response = $this->post(route('store.settings'),[
            'id_uf233' => [10, 11, 12]
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('infusionsoft_merchant_ids', [
            'account' => 'uf233',
            'ids' => json_encode([10, 11, 12])
        ]);
    }
}
