<?php

function api_usuario_put($request) {

    $user = wp_get_current_user();
    $user_id = $user->ID;

    if($user_id > 0) {
        $nome = sanitize_text_field($request['nome']);
        $email = sanitize_email($request['email']);
        $senha = $request['senha'];
        $rua = sanitize_text_field($request['rua']);
        $cep = sanitize_text_field($request['cep']);
        $estado = sanitize_text_field($request['estado']);

        $email_exists = email_exists($email);

        if(!$email_exists || !$email_exists === $user_id) {

            $response = array(
                'ID' => $user_id,
                'user_pass' => $senha,
                'user_email' => $email,
                'display_name'=> $nome,
                'first_name'=> $nome,
            );

            wp_update_user($response);

            update_user_meta($user_id, 'cep', $cep);
            update_user_meta($user_id, 'rua', $rua);
            update_user_meta($user_id, 'estado', $estado);

        } else {
            $response = new WP_Error('email', 'Email já cadastrado', array('status' => 403));
        }
    } else {
        $response = new WP_Error('permissão', 'Usuário não possui permissão', array('status' => 401));
    }
    return rest_ensure_response($response);
}

function registrar_api_usuario_put() {
    register_rest_route('api', '/usuario', array(
        array(
            //'methods' => 'GET',
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => 'api_usuario_put'
        )
    ));
}

add_action('rest_api_init', 'registrar_api_usuario_put');

?>