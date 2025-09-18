<?php
/**
 * Sistema de Rotas Simples
 * Apenas funções helper para URLs
 */

/**
 * Gera URL para uma rota
 */
function route_url($path) {
    return base_url($path);
}

/**
 * Redireciona para uma rota
 */
function redirect_to($route, $statusCode = 302) {
    redirect(route_url($route), $statusCode);
}
?>