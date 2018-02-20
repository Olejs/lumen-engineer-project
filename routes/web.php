<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('v1/auth', ['uses' => 'AuthController@login']);
$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->group(['middleware' => ['JWTauth']], function () use ($router) {
        $router->get('auth', ['uses' => 'AuthController@refresh']);
        $router->post('conference/organizers', ['uses' => 'OrganizersController@confirm']);
        $router->group(['middleware' => ['Organizer']], function () use ($router) {
            $router->post('conference', ['uses' => 'ConferenceController@init']);
            $router->put('conference', ['uses' => 'ConferenceController@edit']);
            $router->post('conference/organizers/invitations', ['uses' => 'InvitationsController@send']);
            $router->get('conference/organizers', ['as' => 'conference', 'uses' => 'OrganizersController@lists']);
            $router->delete('conference/organizers/{userId}', ['as' => 'conference', 'uses' => 'UsersController@delete']);
            $router->post('mail', ['as' => 'conference', 'uses' => 'MailController@send']);
            $router->post('list/mail', ['as' => 'conference', 'uses' => 'MailController@listing']);
            $router->patch('conference/status', ['uses' => 'ConferenceController@setStatus']);
            $router->patch('participants/{id}', ['uses' => 'GuestsController@setState']);
            $router->get('participants', ['uses' => 'GuestsController@listAll']);
            $router->delete('participants/{id}', ['uses' => 'GuestsController@delete']);
            $router->post('events', ['uses' => 'EventsController@add']);
            $router->put('events/{id}', ['uses' => 'EventsController@update']);
            $router->get('events', ['uses' => 'EventsController@getAll']);
            $router->delete('events/{id}', ['uses' => 'EventsController@remove']);

            $router->post('sections', ['uses' => 'SectionsController@add']);
            $router->put('sections/{id}', ['uses' => 'SectionsController@update']);
            $router->get('sections', ['uses' => 'SectionsController@getAll']);
            $router->delete('sections/{id}', ['uses' => 'SectionsController@remove']);

            $router->post('agenda/{eventId}', ['uses' => 'AgendaController@add']);
            $router->get('agenda', ['uses' => 'AgendaController@getAll']);
        });
    });
    $router->get('participants/file', ['uses' => 'GuestsController@export']);
    $router->get('agenda/file', ['uses' => 'EventsController@export']);
    $router->get('conference', ['as' => 'conference', 'uses' => 'ConferenceController@exist']);
    $router->post('users', ['as' => 'conference', 'uses' => 'UsersController@register']);
    $router->post('participants', ['as' => 'conference', 'uses' => 'GuestsController@register']);
});
