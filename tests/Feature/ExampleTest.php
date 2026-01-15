<?php

it('redirects to dashboard', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
});
