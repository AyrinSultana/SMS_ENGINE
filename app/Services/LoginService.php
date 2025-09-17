<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class LoginService
{
    /**
     * Authenticate user via API
     *
     * @param string $id
     * @return array
     */
    public function authenticateUser(string $id): array
    {
        $url = "http://10.124.9.24:8080/panel2/api/api_private";
        $postdata = http_build_query([
            'public_key' => $id,
            'private_key' => 'BX12345CP',
        ]);
        
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata,
            ]
        ];
        
        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);
        
        return $data;
    }
    
    /**
     * Store user data in session
     *
     * @param array $userData
     * @return bool
     */
    public function storeUserSession(array $userData): bool
    {
        if (empty($userData) || !isset($userData['statusCode']) || $userData['statusCode'] !== '200') {
            return false;
        }
        
        $i = 0; // First record
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Map user data to session
        $_SESSION['user'] = $userData['data'][$i]['username'] ?? '';
        $_SESSION['username'] = $userData['data'][$i]['username'] ?? '';
        $_SESSION['cn'] = $userData['data'][$i]['cn'] ?? '';
        $_SESSION['title'] = $userData['data'][$i]['title'] ?? '';
        $_SESSION['department'] = $userData['data'][$i]['department'] ?? '';
        $_SESSION['mobile'] = $userData['data'][$i]['mobile'] ?? '';
        $_SESSION['misys'] = $userData['data'][$i]['misys'] ?? '';
        $_SESSION['employee'] = $userData['data'][$i]['employee'] ?? '';
        $_SESSION['role'] = $userData['data'][$i]['role'] ?? '';
        $_SESSION['ip'] = $userData['data'][$i]['ip'] ?? '';
        $_SESSION['functional_designation'] = $userData['data'][$i]['functional_designation'] ?? '';
        $_SESSION['division'] = $userData['data'][$i]['division'] ?? '';
        $_SESSION['branchCode'] = $userData['data'][$i]['branchCode'] ?? '';
        $_SESSION['branchName'] = $userData['data'][$i]['branchName'] ?? '';
        $_SESSION['logout_url'] = $userData['data'][$i]['logout_url'] ?? '';
        $_SESSION['success'] = 'SUCCESS';
        $_SESSION['msg'] = null;
        $_SESSION['error'] = null;
        $_SESSION['pick'] = null;
        
        return true;
    }
}
