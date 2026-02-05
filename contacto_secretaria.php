<?php include 'head.php'; ?>

<style>
    /* CONTACTO - CSS EMBEBIDO */
    :root {
        --verde-principal: #4caf50;
        --verde-oscuro: #388e3c;
        --blanco: #ffffff;
    }

    .contacto-contenido {
        max-width: 800px;
        margin: 0 auto;
    }

    .contacto-card {
        background: var(--blanco);
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        border: 1px solid rgba(76, 175, 80, 0.12);
    }

    .contacto-info {
        margin: 2rem 0;
    }

    .contacto-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: rgba(76, 175, 80, 0.04);
        border-radius: 8px;
        border-left: 4px solid var(--verde-principal);
    }

    .contacto-item i {
        color: var(--verde-principal);
        font-size: 1.2rem;
        margin-top: 0.2rem;
        flex-shrink: 0;
    }

    .contacto-item strong {
        color: var(--verde-oscuro);
        display: block;
        margin-bottom: 0.25rem;
    }

    .enlace-correo {
        color: var(--verde-principal) !important;
        font-weight: 500;
        text-decoration: none;
        word-break: break-word;
    }

    .enlace-correo:hover {
        text-decoration: underline;
    }

    .aviso-importante {
        background: linear-gradient(135deg, #fff5f5 0%, #ffebee 100%);
        border: 2px solid #ffcdd2;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
        text-align: center;
    }

    .aviso-importante i {
        color: #e57373;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .aviso-importante h3 {
        color: #c62828;
        margin: 0 0 1rem 0;
        font-size: 1.2rem;
    }

    .aviso-importante p {
        margin: 0;
        color: #d32f2f;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .contacto-card {
            padding: 2rem 1.5rem;
        }
        
        .contacto-item {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>

<!-- CABECERA DE SECCIÓN -->
<section class="erasmus-contenido">
    <div class="contenedor-max">
        <div class="avisos-layout">
            <div class="avisos-logo">
                <i class="fas fa-phone" style="font-size: 3.5rem; color: var(--verde-principal);"></i>
            </div>
            <div class="avisos-texto">
                <h2>CONTACTO SECRETARÍA</h2>
                <p>Información y atención al público</p>
            </div>
        </div>
    </div>
</section>

<!-- CONTENIDO CONTACTO -->
<main>
    <section class="seccion-contenido">
        <div class="contenedor-max">
            <div class="contacto-contenido">
                <div class="contacto-card">
                    <h2 class="seccion-contenido-h2">Para contactar con secretaría:</h2>
                    
                    <div class="contacto-info">
                        <div class="contacto-item">
                            <i class="fas fa-phone"></i>
                            <strong>Teléfono:</strong> 916 43 99 91
                        </div>
                        
                        <div class="contacto-item">
                            <i class="fas fa-fax"></i>
                            <strong>Fax:</strong> 916 44 00 25
                        </div>
                        
                        <div class="contacto-item">
                            <i class="fas fa-clock"></i>
                            <strong>Horario de atención al público:</strong><br>
                            Días laborables de lunes a viernes. De 9:30 a 12:00 horas.
                        </div>
                        
                        <div class="contacto-item">
                            <i class="fas fa-envelope"></i>
                            <strong>Correo:</strong><br>
                            <a href="mailto:secretaria.ies.laarboleda.alcorcon@educa.madrid.org" class="enlace-correo">
                                secretaria.ies.laarboleda.alcorcon@educa.madrid.org
                            </a>
                        </div>
                    </div>

                    <div class="aviso-importante">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>AVISO IMPORTANTE</h3>
                        <p>No se responderán mediante correo electrónico aquellas solicitudes de información que se encuentren ya publicadas en la página web.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
