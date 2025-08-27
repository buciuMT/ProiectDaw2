<?php

class HomeController extends Controller {
    
    public function index() {
        $data = [
            'title' => 'Welcome to Lib4All',
            'message' => 'Library Management System for Everyone'
        ];
        
        $this->view('home', $data);
    }
    
    public function about() {
        $data = [
            'title' => 'About Lib4All',
            'message' => 'Lib4All is a library management system designed for everyone'
        ];
        
        $this->view('about', $data);
    }
}