<?php

it('redirects the public root to the local admin login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
