<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoginService;

class LoginController extends Controller
{
    protected $loginService;
    
    /**
     * Constructor
     *
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }
    
    /**
     * Authenticate user via API
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function user_access($id)
    {
        // Get user data from API
        $userData = $this->loginService->authenticateUser($id);
        
        // For debugging
        // dd($userData);
        
        // Store user data in session
        $authenticated = $this->loginService->storeUserSession($userData);
        
        if ($authenticated) {
            return redirect('index.php');
        } elseif (isset($userData['statusCode']) && $userData['statusCode'] == '103') {
            return redirect('http://192.168.1.96/ificaamarapps/index.php');
        } else {
            return redirect('http://192.168.1.96/ificaamarapps/index.php');
        }
    }
}
     
    




