<?php
namespace App\Listeners\Gamification;

use App\Events\User\UserCreatedThroughExternalFunnel;
use App\Models\UserPointActivity;
use Gamification\Gamification;

/**
 * This is a temporary listener to catch registrants coming from
 * the Lurn 10x event who purchased Digital Lead Academy. Since it
 * only needs to account for that one event, the list of emails
 * is hardcoded.
 */
class AwardLurn10xFunnelPoints
{
    public function handle(UserCreatedThroughExternalFunnel $event)
    {
        // If this isn't the correct funnel, exit event.
        if (strtolower($event->funnel) !== 'lurn10x') {
            return;
        }

        // See if the user's email address matches the Lurn10x emails.
        if (! in_array($event->user->email, $this->listFrom10x)) {
            return;
        }

        $description = "Signed up through Lurn10x!";

        $activity = UserPointActivity::where('user_id', $event->user->id)
            ->where('transaction', $description)
            ->count();

        // Make sure the user hasn't been awarded for this before.
        if ($activity) {
            return;
        }

        $api = new Gamification();

        $api->buyCourse([
            'userId'      => $event->user->id,
            'email'       => $event->user->email,
            'description' => $description,
            'points'      => 300,
            'details'     => [
                'course_type'  => 'free',
                'course_price' => '0.00'
            ],
        ]);
    }

    protected $listFrom10x = [
        'ada.arvizo@bancorp.com',
        'Admin@cakestoday.co.uk',
        'ajr194@gmail.com',
        'alexander@krajnak.sk',
        'allen.jablonski@gmail.com',
        'Alpsburysolutions@gmail.com',
        'Alyssamccoyrehling@gmail.com',
        'amphotographyfilm@gmail.com',
        'andrew.wiseman@wisemanlawyers.com.au',
        'angelasw6563@gmail.com',
        'arnold@magnoliapark.net',
        'Asianginger@yahoo.com',
        'awilson@callcarpenter.com',
        'Brandin.Pettersen@gmail.com',
        'bullockauctioneers@sbcglobal.net',
        'carlos@latamready.com',
        'ceo@normanhubbard.com',
        'Chad5bmw@gmail.com',
        'Claudio@toyamaco.com',
        'Closewithlance@gmail.com',
        'cookkennen@gmail.com',
        'Cory@sellasisfast.com',
        'Dale@keytothebay.com',
        'dan@aclearviewwindowcleaning.com',
        'david@teamfranco.biz',
        'deartina0426@gmail.com',
        'Dennis@lebaronmarketing.com',
        'dkropf89@gmail.com',
        'DNG.directv@gmail.com',
        'Doug@locascannahouse.com',
        'Drjf14@aol.com',
        'drjosh300@gmail.com',
        'Drtaxlien@gmail.com',
        'dylan.k@wealthwatchadvisors.com',
        'Dylan@ctenow.com',
        'Dylansingleton77@yahoo.com',
        'Eddieenriquez44@yahoo.com',
        'Emilykvu@gmail.com',
        'emilyveles@gmail.com',
        'epelletier@laporteconsultants.com',
        'Falidadidds@aol.com',
        'felipe@safeboundlogistics.com',
        'gerald_18@hotmail.com',
        'grant@perfectpropertiesgroup.com',
        'guanunagary@gmail.com',
        'Hank3@hankcorrea.com',
        'hoefs@jungehaie.com',
        'inavatetime@gmail.com',
        'info@bestofbest.eu',
        'J.dgauthier27@gmail.com',
        'Jaime@integraelectrical.com',
        'Jasonwherbert@gmail.com',
        'jay@robcofenceanddeck.com',
        'jblima7@gmail.com',
        'jc@redero.com',
        'jcrooksjr@gmail.com',
        'Jeffory.sweat@gmail.com',
        'jellison@ephoenixlink.com',
        'jeotremaria87@yahoo.es',
        'jholland26@gmail.com',
        'jlatimer@notaxdue.com',
        'JLesley.c21@gmail.com',
        'Jmusgrov@amfam.com',
        'Joedoyle1a@gmail.com',
        'joekosc@gmail.com',
        'JosephNoah@JosephNoah.com',
        'Journey5.allison@gmail.com',
        'jsheehan@synchronet.net',
        'jt@neverstopwinningcollective.com',
        'juli_stark@yahoo.com',
        'kentonwickersham@gmail.com',
        'kevcallahan@gmail.com',
        'Ladylegendketoempire@gmail.com',
        'lepperte@gmail.com',
        'lighthouses33@comcast.net',
        'luis@luckygameslimited.com',
        'luz.tabajonda@gmail.com',
        'm.huxford89@gmail.com',
        'mackdoby@gmail.com',
        'Malinda@openroadgirl.com',
        'marcjlogan@gmail.com',
        'mark@breathelife.co.nz',
        'Markk.gonzales@cssdtv.com',
        'mattelling@gmail.com',
        'Mchaudhary@alarmguard.ca',
        'michaelh@hibbards.com.au',
        'Mike.C.Hayashi@gmail.com',
        'monicabusby@gmail.com',
        'mosmoothies@aol.com',
        'MTI.enterprises.inc@gmail.com',
        'Nanette@trainingdr.com',
        'Nattiboy93@gmail.com',
        'navarro1333@gmail.com',
        'navpataria@gmail.com',
        'neelraguboopathi@gmail.com',
        'Northernstarrei@gmail.com',
        'Pickbrad@gmail.com',
        'Praveen@customcreativeaz.com',
        'pro_services@live.com',
        'r2rllc@yahoo.com',
        'Ricardo.rosales@sameascash.com',
        'richmud@gmail.com',
        'Rick2sbr@gmail.com',
        'Robertreza@hotmail.com',
        'robles_alfredo@yahoo.com',
        'rogertrandds@yahoo.com',
        'Romeiserd@aol.com',
        'Ross@rewardspointer.com',
        'Rubysadler@aol.com',
        's.lutz@kilag.ch',
        'Sabrinalloyd@lloydagencies.com3',
        'sacjuarezagent@gmail.com',
        'sal2@live.com',
        'Scott@corpstrat.com',
        'sean@youclaimit.net',
        'seanbelisle@gmail.com',
        'sgproperties101@gmail.com',
        'shane@mossyoakfences.com',
        'shawn.mantel@powersafeautomation.com',
        'spl221@yahoo.com',
        'ssjorlando53@yahoo.com',
        'Steven.nexxsteel@gmail.com',
        'Stran@sunsourcefinancial.com',
        'support4you@comcast.net',
        'syoung737@yahoo.com',
        'talston@aeromarinetaxpros.com',
        'tarry@summersdevgroup.com',
        'Tclark@southlandwholesale.com',
        'tim@tisalesgroup.com',
        'tradetraveltriumph@outlook.com',
        'troyifit@gmail.com',
        'Truongtran20171979@gmail.com',
        'trwar296@gmail.com',
        'tshanberg@gmail.com',
        'tucan308@gmail.com',
        'Tuutapulotu@gmail.com',
        'vlad@daviogroup.com',
        'vyhenriquez@gmail.com',
        'Wdbgman@yahoo.com',
        'weekswellness@gmail.com',
        'wfg.nklein@gmail.com',
        'zachhallzie@gmail.com',
        'zappsmarketplace@gmail.com',
        'zuriwest@gmail.com'
    ];
}
