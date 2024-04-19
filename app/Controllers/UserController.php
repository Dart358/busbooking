<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class UserController extends ResourceController // UserController class that extends the ResourceController class
{

    protected $modelName = 'App\Models\UserModel'; // The model to use
    protected $format    = 'json'; // The format of the response

    public function register() { // Method to register a user
        // Implementation goes here

    $rules = [ // Validation rules
        'name' => 'required|min_length[3]|max_length[20]', // Name is required, minimum length is 3, maximum length is 20
        'email' => 'required|valid_email|is_unique[users.email]', // Email is required, must be a valid email, and must be unique in the users table
        'password' => 'required|min_length[8]' // Password is required, minimum length is 8
    ];

    $input = $this->request->getPost(); // Get the POST data

    if (!$this->validate($rules)) { // If validation fails
        return $this->fail($this->validator->getErrors()); // Return the validation errors
    }

    $data = [ // Data to be saved
        'name' => $input['name'], // Name
        'email' => $input['email'], // Email
        'password' => $input['password'] // Password
    ];

    $model = new UserModel(); // Create a new UserModel instance
    $model->save($data); // Save the data

    return $this->respondCreated($data, 'User registered'); // Return a response indicating the user was registered
}

    public function login() { // Method to login a user
       
    $rules = [ // Validation rules
        'email' => 'required|valid_email', // Email is required and must be a valid email
        'password' => 'required|min_length[8]' // Password is required and minimum length is 8
    ];

    $input = $this->request->getPost(); // Get the POST data

    if (!$this->validate($rules)) { // If validation fails
        return $this->fail($this->validator->getErrors()); // Return the validation errors
    }

    $model = new UserModel(); // Create a new UserModel instance
    $user = $model->where('email', $input['email'])->first(); // Get the first user with the provided email

    if (!$user || !password_verify($input['password'], $user['password'])) { // If no user was found or the password is incorrect
        return $this->fail('Invalid credentials'); // Return an error
    }

    return $this->respond($user); // Return the user data
}

    public function updateProfile($id) { // Method to update a user's profile
    $rules = [ // Validation rules
        'name' => 'permit_empty|min_length[3]|max_length[20]', // Name can be empty, minimum length is 3, maximum length is 20
        'email' => 'permit_empty|valid_email|is_unique[users.email,user_id,{id}]' // Email can be empty, must be a valid email, and must be unique in the users table
    ];

    $input = $this->request->getRawInput();  // Get the raw input data for PATCH or PUT request

    if (!$this->validate($rules)) { // If validation fails
        return $this->fail($this->validator->getErrors()); // Return the validation errors
    }

    $data = []; // Data to be updated
    if (!empty($input['name'])) { // If name is provided
        $data['name'] = $input['name']; // Set name
    }
    if (!empty($input['email'])) { // If email is provided
        $data['email'] = $input['email']; // Set email
    }

    $model = new UserModel(); // Create a new UserModel instance
    if ($model->update($id, (object)$data)) { // If the update is successful
        return $this->respondUpdated($data, 'User profile updated'); // Return a response indicating the user profile was updated
    } else {
        return $this->failServerError('Unable to update user profile'); // Return an error
    }
}

    public function deleteUser($id) { // Method to delete a user
    $model = new UserModel(); // Create a new UserModel instance
    $userData = $model->find($id); // Find the user with the provided ID

    if (!$userData) { // If no user was found
        return $this->failNotFound('No user found with ID: ' . $id); // Return an error
    }

    if ($model->delete($id)) { // If the delete is successful
        return $this->respondDeleted('User deleted successfully'); // Return a response indicating the user was deleted
    } else {
        return $this->failServerError('Failed to delete user'); // Return an error
    }
}

}
