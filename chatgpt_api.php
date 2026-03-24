<?php
header('Content-Type: application/json'); // Indica al navegador que respuesta es JSON

// API Key gratuita de Groq (obtenida en console.groq.com)
$GROQ_API_KEY = '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';

if (!empty($_POST['message'])) { // Verifica que POST tenga campo 'message'
    $message = trim($_POST['message']); // Limpia espacios inicio/fin del mensaje
    $msg_lower = strtolower($message); // Convierte mensaje a minúsculas para búsquedas
    
    // PRIORIDAD 1: Intento Groq (IA inteligente)
    $groq_response = callGroqAPI($message); // Llama función Groq API

    if ($groq_response && !empty($groq_response)) { // Si Groq responde correctamente
        echo json_encode(['response' => $groq_response]); // Devuelve respuesta IA
        exit; // Termina ejecución inmediatamente
    }

    // PRIORIDAD 2: Fallback respuestas locales
    $local_response = getLocalResponse($msg_lower); // Respuestas fijas si IA falla
    echo json_encode(['response' => $local_response]); // Devuelve respuesta local
} else {
    echo json_encode(['error' => 'No message']); // Error si no hay mensaje
}

/* Función principal: conexión con Groq AI
 *  Usa LLaMA 3.1 gratis con conocimiento LIMITADO a estructura web */
function callGroqAPI($message) {
    global $GROQ_API_KEY; // Usa clave API definida arriba
    
    // SYSTEM PROMPT: Limitar IA SOLO a estructura navegación IES La Arboleda
    $systemPrompt = "Eres ArboledaBot. SOLO conoces estructura navegación IES La Arboleda.\\n\\n" .
            "SI NO SABES algo de la estructura web → 'Lo siento, ¿puedes ser mas especifico?'\\n\\n" .
            "Quiero que des respuestas humanizadas SIEMPRE y faciles de entender\\n\\n" .
            "=== ESTRUCTURA WEB ===\\n" .
            "MENÚ PRINCIPAL:\\n" .
            "• Inicio (es donde se encuentra el aula virtual, el correo educamadrid y roble/raices en la seccion a un clic; tambien las ultimas noticias y lo relevante ahora)\\n" .
            "• Nuestro centro → Organigrama (donde se encuentra los cargos y profesores y docentes)| AMPA | Resultados académicos | Bolsa empleo | Teatro | Plan igualdad\\n" .
            "• Oferta educativa → ESO | Bachillerato (las diferentes modalidades que hay)| FP (informacion sobre ciclos y grados) | Curso Desarrollo de Videojuegos\\n" .
            "• Secretaría → Avisos | Matriculación(ESO/Bach/FP) | Convalidación(ESO/Bach/FP) | Solicitud de Títulos (eso/bachillerato/fp)| Contacto\\n" .
            "• Departamentos | Erasmus+ | Info familias | Documentos | Orientación\\n\\n" .
            
            "RESPUESTA FORMATO: 'En la seccion Secretaría → Matriculación → FP'\\n" .
            "NUNCA: URLs, colores, horarios, teléfonos, datos personales.";

    // Payload JSON para API Groq (estructura de datos que envía)
    $payload = [// Datos que envía a Groq
        'model' => 'llama-3.1-8b-instant', // Modelo IA gratuito y rápido
        'messages' => [// Conversación completa
            ['role' => 'system', 'content' => $systemPrompt], // Instrucciones fijas para IA
            ['role' => 'user', 'content' => $message] // Mensaje del usuario
        ],
        'stream' => false, // Espera respuesta completa (no streaming)
        'temperature' => 0.1, // Bajar temperatura = respuestas muy predecibles
        'max_tokens' => 80 // Limita respuesta a máximo 80 palabras
    ];

    // Configuración CURL para petición HTTP a Groq
    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions'); // Inicia conexión CURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Captura respuesta como string
    curl_setopt($ch, CURLOPT_POST, true); // Usa método POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); // Envía datos JSON
    curl_setopt($ch, CURLOPT_HTTPHEADER, [// Headers HTTP obligatorios
        'Content-Type: application/json', // Formato datos enviados
        'Authorization: Bearer ' . $GROQ_API_KEY // Autenticación API
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Espera máximo 15 segundos
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desactiva verificación SSL (desarrollo)

    $response = curl_exec($ch); // Ejecuta petición HTTP a Groq
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtiene código respuesta HTTP
    $curlError = curl_error($ch); // Captura errores CURL si los hay
    curl_close($ch); // Cierra conexión CURL

    // Loggea en archivo error para debug
    error_log("Groq HTTP:$httpCode | Error:$curlError | Msg:$message");
    
    // Procesa respuesta SOLO si HTTP 200 y hay datos
    if ($httpCode == 200 && $response) {
        $decoded = json_decode($response, true); // Convierte JSON respuesta a array PHP
        if (isset($decoded['choices'][0]['message']['content'])) { // Verifica estructura respuesta
            return trim($decoded['choices'][0]['message']['content']); // Devuelve texto limpio IA
        }
    }

    return false; // Groq falló → usar respuestas locales
}

/* Fallback local: respuestas fijas SOLO estructura web
 * Sin URLs, datos personales, horarios, etc. */
function getLocalResponse($msg) {
    // Busca palabras clave en mensaje usuario y devuelve ruta web
    if (strpos($msg, 'matricul') !== false || strpos($msg, 'matrícula') !== false) {
        return "Secretaría → Matriculación"; // Respuesta para matriculaciones
    } elseif (strpos($msg, 'aula') !== false || strpos($msg, 'virtual') !== false) {
        return "HOME → A UN CLIC"; // Ruta aula virtual Educamadrid
    } elseif (strpos($msg, 'secretar') !== false || strpos($msg, 'secretaría') !== false) {
        return "Secretaría → Contacto"; // Contacto secretaría
    } elseif (strpos($msg, 'ampa') !== false) {
        return "Nuestro centro → AMPA"; // Página AMPA
    } elseif (strpos($msg, 'organigrama') !== false || strpos($msg, 'profesor') !== false) {
        return "Nuestro centro → Organigrama"; // Organigrama profesores
    } elseif (strpos($msg, 'erasmus') !== false) {
        return "Menú principal → Erasmus+"; // Erasmus sección
    } elseif (strpos($msg, 'convalidacion') !== false) {
        return "Secretaría → Convalidación"; // Convalidaciones títulos
    } elseif (strpos($msg, 'daw') !== false || strpos($msg, 'desarrollo') !== false || strpos($msg, 'fp') !== false) {
        return "Oferta educativa → FP"; // Formación Profesional
    } else {
        return "No sé ubicación"; // Respuesta por defecto
    }
}
?>
