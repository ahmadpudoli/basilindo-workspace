<?php

it('renders the public Basilindo landing page', function () {
    $response = $this->get('/');

    $response->assertOk();
});
