<?php

namespace UserBundle\Controller\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use UserBundle\Entity\User;
use UserBundle\Service\MyUserManager;

/**
 * @author Alexander
 */
class BaseController extends Controller
{

    /**
     * @Route("/", name="default_route")
     */
    public function defaultAction(Request $request)
    {
        return new Response('Hello here!');
    }

    /**
     * @Route("/test/reg", name="test_reg_route")
     */
    public function testRegistrationAction(Request $request)
    {
//         'username',
//            'email',
//            'city_id',
//            'cargo_type',
//            'phone',
//            'city_district',
//            'description',
//            'dimensions',
//            'loaders',
//            'work_area',
//            'auto_type',
//            'price',
//            'min_hour',
//            'work_time',

        $ch = curl_init();
        $url = "http://carriers/api/v1/register";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0); // читать заголовок
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу

        $post = [
            'username' => 'user',
            'password' => 'passuser1',
            'city_id' => 1,
            'cargo_type' => 'cargo_300',
            'phone' => 33333312321321,
            'city_district' => 'Октябрьский, ЗАводской',
            'description' => 'DASDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD',
            'loaders' => 1,
            'work_area' => json_encode(array('city' => 1, 'suburban' => 1)),
            'auto_type' => 'ГАЗЕЛЬКА',
            'price' => 100,
            'min_hour' => 10,
            'work_time' => json_encode(array(123132132132321)),
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        print_r(json_decode($content, true));
        exit;
    }

    /**
     * @Route("/test/login", name="test_login_route")
     */
    public function testLoginAction(Request $request)
    {
        $ch = curl_init();
        $url = "http://carriers/app_dev.php/api/v1/login";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0); // читать заголовок
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу

        $post = [
            'password' => 'passuser1',
            'phone' => 33333312321321,
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);
        
        echo $content;

//        print_r(json_decode($content, true));
        exit;
    }

}
