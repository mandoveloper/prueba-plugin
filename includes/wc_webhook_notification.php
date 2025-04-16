<?php

function manejar_webhook_notificacion() {
    global $wp_query;

    if (isset($wp_query->query_vars['webhook_atix_payment'])) {
        // Validar si la solicitud es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener el security_key del plugin
            $atixGateway = new WC_Gateway_Atix();
            
            $finalStatusTransaction = $atixGateway->finalStatus;

            if(isset($atixGateway->securityKey)){
                if(!empty($atixGateway->securityKey)){
                    $my_webhook_security_key = $atixGateway->securityKey;
                }else{
                    wp_send_json_error('Verifique que su security_key este configurada correctamente.', 403);
                }
            }
    
            // Capturar los datos recibidos
            $input = file_get_contents("php://input");
            $data = json_decode($input, true);
            // wp_send_json_success('parametros Body:: '.json_encode($data), 200);
            
            
            if ($data && isset($data['event'])) {
                
                $event_payment = $data['event'];
                $signature = $data['signature'];
                $reference_id = $data['data']['reference'];
                $status_transaction = $data['data']['result_transaction_code'];
                
                // Validar parámetros de entrada
                
                if (empty($reference_id)) {
                    wp_send_json_error('El parametro "result_transaction_code" es requerido.', 400);
                }
                
                if (empty($status_transaction)) {
                    wp_send_json_error('El parametro "result_transaction_code" es requerido.', 400);
                }
                
                if (empty($signature)) {
                    wp_send_json_error('El parametro "signature" es requerido.', 400);
                }
                
                
                // firmado
                $concatenated_string = $event_payment . $reference_id . $status_transaction . $my_webhook_security_key;
                $hash = hash('sha256', $concatenated_string);
                
                //verificar que las firmas coincidan
                if($hash !== $signature){
                    wp_send_json_error('Firma no valida.', 401);
                }
                
                // Actualizar el pedido en WooCommerce
                $order = wc_get_order($reference_id);

                if ($order) {
                    // Actualizar el estado del pedido
                    if ($status_transaction === "00") {
                        $order->update_status($finalStatusTransaction, 'Pago recibido.');
                    } else {
                        $order->update_status('failed', 'No se pudo procesar el pago.');
                    }

                } else {
                    wp_send_json_error('No se pudo encontrar el pedido: ' . $reference_id, 404);
                }
                
            } else {
                if (empty($data['event_payment'])) {
                     wp_send_json_error('El parametro "event_payment" es requerido.', 400);
                }
            }
            
            $response = array(
                        'order_id' => $order->get_id(),
                        'status' => $order->get_status(),
                        'message' => 'Pedido actualizado correctamente.'
                    );
                    
            wp_send_json_success($response);

        } else {
            wp_send_json_error('Método no permitido.', 405);
        }
        exit;
    }
}
add_action('template_redirect', 'manejar_webhook_notificacion');