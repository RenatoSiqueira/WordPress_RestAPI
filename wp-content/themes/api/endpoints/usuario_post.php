<?php

function api_usuario_post($request) {

    $nome = sanitize_text_field($request['nome']);
    $email = sanitize_email($request['email']);
    $senha = $request['senha'];
    $rua = sanitize_text_field($request['rua']);
    $cep = sanitize_text_field($request['cep']);
    $estado = sanitize_text_field($request['estado']);

    $user_exists = username_exists($email);
    $email_exists = email_exists($email);

    if(!$user_exists && !$email_exists && $email && $senha) {
        $user_id = wp_create_user($email, $senha, $email);

        $response = array(
            'ID' => $user_id,
            'display_name'=> $nome,
            'first_name'=> $nome,
            'role' => 'subscriber'
        );

        wp_update_user($response);

        update_user_meta($user_id, 'cep', $cep);
        update_user_meta($user_id, 'rua', $rua);
        update_user_meta($user_id, 'estado', $estado);

    } else {
        $response = new WP_Error('email', 'Email já cadastrado', array('status' => 403));
    }

    // $response = array(
    //     'nome' => $nome,
    //     'email' => $email,
    //     'senha' => $senha,
    //     'rua' => $rua,
    //     'cep' => $cep,
    //     'estado' => $estado,
    // );

    return rest_ensure_response($response);
}

function registrar_api_usuario_post() {
    register_rest_route('api', '/usuario', array(
        array(
            //'methods' => 'GET',
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'api_usuario_post'
        )
    ));
}

add_action('rest_api_init', 'registrar_api_usuario_post');

?>