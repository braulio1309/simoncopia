<footer class="site__footer">
    <div class="site-footer">
        <div class="decor site-footer__decor decor--type--bottom">
            <div class="decor__body">
                <div class="decor__start"></div>
                <div class="decor__end"></div>
                <div class="decor__center"></div>
            </div>
        </div>
        <div class="site-footer__widgets">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-xl-2">
                        <div class="site-footer__widget footer-contacts">
                            <h5 class="footer-contacts__title">Nuestra Ubicación</h5>
                            <address class="footer-contacts__contacts">
                                <dl>
                                    <dt>CEDI</dt>
                                    <dd><?php echo $this->config->item('direccion_cedi'); ?></dd>
                                </dl><br>

                                <dl>
                                    <dt>Sede Itagüí</dt>
                                    <dd><?php echo $this->config->item('direccion_itagui'); ?></dd>
                                </dl>

                                <dl>
                                    <dt>Sede Girardota</dt>
                                    <dd><?php echo $this->config->item('direccion_girardota'); ?></dd>
                                </dl>

                                <dl>
                                    <dt>Sede Marinilla</dt>
                                    <dd><?php echo $this->config->item('direccion_marinilla'); ?></dd>
                                </dl>

                                <dl>
                                    <dt>Sede Dosquebradas</dt>
                                    <dd><?php echo $this->config->item('direccion_dos_quebradas'); ?></dd>
                                </dl>
                            </address>
                        </div>
                    </div>
                    <div class="col-12 col-xl-2">
                        <div class="site-footer__widget footer-contacts">
                            <h5 class="footer-contacts__title">Contáctanos</h5>
                            <address class="footer-contacts__contacts">
                                <dl>
                                    <dt>Número telefónico</dt>
                                    <dd><?php echo $this->config->item('telefono'); ?></dd>
                                </dl><br>
                                <dl>
                                    <dt>Correo electrónico</dt>
                                    <dd><?php echo $this->config->item('email'); ?></dd>
                                </dl>
                                <dl>
                                    <dt>Horarios de atención</dt>
                                    <dd><?php echo $this->config->item('horario_semana'); ?></dd>
                                    <dd><?php echo $this->config->item('horario_fines_semana'); ?></dd>
                                </dl>
                            </address>
                        </div>
                    </div>
                    <div class="col-6 col-xl-2">
                        <div class="site-footer__widget footer-links">
                            <h5 class="footer-links__title">Información</h5>
                            <ul class="footer-links__list">
                                <li class="footer-links__item"><a href="<?php echo site_url('blog/nosotros'); ?>" class="footer-links__link">Acerca de nosotros</a></li>
                                <li class="footer-links__item"><a href="<?php echo site_url('blog/contacto'); ?>" class="footer-links__link">Contacto</a></li>
                                <li class="footer-links__item"><a href="<?php echo site_url('blog/taller_aliado'); ?>" class="footer-links__link">Taller aliado</a></li>
                                <li class="footer-links__item"><a href="<?php echo site_url('blog/tratamiento_datos'); ?>" class="footer-links__link">Política de tratamiento de datos</a></li>
                                <li class="footer-links__item"><a href="<?php echo site_url('blog/tratamiento_cookies'); ?>" class="footer-links__link">Política de cookies</a></li>
                                <li class="footer-links__item"><a href="<?php echo site_url('blog/politica_envios'); ?>" class="footer-links__link">Política de envíos y devoluciones</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-6 col-xl-2">
                        <div class="site-footer__widget footer-links">
                            <h5 class="footer-links__title">Mi cuenta</h5>
                            <!-- <ul class="footer-links__list">
                                <li class="footer-links__item"><a href="<?php // echo site_url('productos?busqueda=outlet'); ?>" class="footer-links__link">Outlet</a></li>
                            </ul> -->
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="site-footer__widget footer-newsletter">
                            <h5 class="footer-contacts__title">Acerca de nosotros</h5>
                            <div class="footer-contacts__text">
                                Somos una empresa de repuestos para vehículos de carga pesada.
                                <hr>

                                Somos importadores de marcas reconocidas como SKF, Mansons, KTC, Cummins, Firestone y Randon, siempre nos esforzamos por ofrecer lo mejor a nuestros clientes.
                            </div><br>

                            <!-- <h5 class="footer-newsletter__title">Newsletter</h5>
                            <div class="footer-newsletter__text">
                                Enter your email address below to subscribe to our newsletter and keep up to date with discounts and special offers.
                            </div>
                            <form action="" class="footer-newsletter__form">
                                <label class="sr-only" for="footer-newsletter-address">Email Address</label>
                                <input type="text" class="footer-newsletter__form-input" id="footer-newsletter-address" placeholder="Email Address...">
                                <button class="footer-newsletter__form-button">Subscribe</button>
                            </form> -->
                            <!-- <div class="footer-newsletter__text footer-newsletter__text--social"></div> -->
                            <h5 class="footer-contacts__title">Síguenos en nuestras redes sociales</h5>
                            <div class="footer-newsletter__social-links social-links">
                                <ul class="social-links__list">
                                    <!-- Facebook -->
                                    <li class="social-links__item social-links__item--facebook">
                                        <a href="https://www.facebook.com/RepuestosSimonBolivar" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                    </li>

                                    <!-- Instagram -->
                                    <li class="social-links__item social-links__item--instagram">
                                        <a href="https://www.instagram.com/repuestossimonbolivar/" target="_blank"><i class="fab fa-instagram"></i></a>
                                    </li>

                                    <!-- Youtube -->
                                    <li class="social-links__item social-links__item--youtube">
                                        <a href="https://www.youtube.com/@repuestossimonbolivarcolombia" target="_blank"><i class="fab fa-youtube"></i></a>
                                    </li>

                                    <!-- Tiktok -->
                                    <li class="social-links__item social-links__item--tiktok">
                                        <a href="https://www.tiktok.com/@repuestossimonbolivar" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-100 -50 700 700">
                                                <path d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/>
                                            </svg>
                                        </a>
                                    </li>

                                    <!-- Threads -->
                                    <li class="social-links__item social-links__item--tiktok">
                                        <a href="https://www.threads.net/@repuestossimonbolivar" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-100 -50 700 700"><path d="M331.5 235.7c2.2 .9 4.2 1.9 6.3 2.8c29.2 14.1 50.6 35.2 61.8 61.4c15.7 36.5 17.2 95.8-30.3 143.2c-36.2 36.2-80.3 52.5-142.6 53h-.3c-70.2-.5-124.1-24.1-160.4-70.2c-32.3-41-48.9-98.1-49.5-169.6V256v-.2C17 184.3 33.6 127.2 65.9 86.2C102.2 40.1 156.2 16.5 226.4 16h.3c70.3 .5 124.9 24 162.3 69.9c18.4 22.7 32 50 40.6 81.7l-40.4 10.8c-7.1-25.8-17.8-47.8-32.2-65.4c-29.2-35.8-73-54.2-130.5-54.6c-57 .5-100.1 18.8-128.2 54.4C72.1 146.1 58.5 194.3 58 256c.5 61.7 14.1 109.9 40.3 143.3c28 35.6 71.2 53.9 128.2 54.4c51.4-.4 85.4-12.6 113.7-40.9c32.3-32.2 31.7-71.8 21.4-95.9c-6.1-14.2-17.1-26-31.9-34.9c-3.7 26.9-11.8 48.3-24.7 64.8c-17.1 21.8-41.4 33.6-72.7 35.3c-23.6 1.3-46.3-4.4-63.9-16c-20.8-13.8-33-34.8-34.3-59.3c-2.5-48.3 35.7-83 95.2-86.4c21.1-1.2 40.9-.3 59.2 2.8c-2.4-14.8-7.3-26.6-14.6-35.2c-10-11.7-25.6-17.7-46.2-17.8H227c-16.6 0-39 4.6-53.3 26.3l-34.4-23.6c19.2-29.1 50.3-45.1 87.8-45.1h.8c62.6 .4 99.9 39.5 103.7 107.7l-.2 .2zm-156 68.8c1.3 25.1 28.4 36.8 54.6 35.3c25.6-1.4 54.6-11.4 59.5-73.2c-13.2-2.9-27.8-4.4-43.4-4.4c-4.8 0-9.6 .1-14.4 .4c-42.9 2.4-57.2 23.2-56.2 41.8l-.1 .1z"/></svg>
                                        </a>
                                    </li>

                                    <!-- Linkedin -->
                                    <li class="social-links__item social-links__item--linkedin">
                                        <a href="https://www.linkedin.com/company/sim%C3%B3n-bol%C3%ADvar" target="_blank"><i class="fab fa-linkedin"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="site-footer__bottom">
            <div class="container">
                <div class="site-footer__bottom-row">
                    <div class="site-footer__copyright">
                        Un desarrollo de <a href="https://johnarleycano.com" target="_blank">John Arley Cano</a> - <?php echo "Versión ".exec('git describe --tags'); ?>
                    </div>
                    <div class="site-footer__payments">
                        <img src="<?php echo base_url(); ?>images/formas_pago_wompi.png" alt="Formas de pago" width="320">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>