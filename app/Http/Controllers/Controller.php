<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Agri-Processing System API",
 *     version="1.0.0",
 *     description="This is the API documentation for the Agri-Processing System, providing a comprehensive guide to all available endpoints, parameters, and responses. It covers master data management, lot processing, quality control, and financial calculations."
 * ),
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Main API Server"
 * )
 */
abstract class Controller
{
    // This is the base controller for the application.
    // All other controllers should extend this class.
    // It can be used to define properties and methods that are common to all controllers.
}
