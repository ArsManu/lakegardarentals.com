import Swiper from 'swiper';
import { A11y, Autoplay, EffectFade, Keyboard } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';

const initHomeHeroSlider = () => {
    document.querySelectorAll('[data-home-hero-slider]').forEach((element) => {
        if (element.dataset.heroSliderBound === '1') {
            return;
        }

        element.dataset.heroSliderBound = '1';

        const slideCount = element.querySelectorAll('.swiper-slide').length;
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        new Swiper(element, {
            modules: [A11y, Autoplay, EffectFade, Keyboard],
            slidesPerView: 1,
            loop: slideCount > 1,
            speed: prefersReducedMotion ? 0 : 900,
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
            allowTouchMove: slideCount > 1,
            keyboard: {
                enabled: slideCount > 1,
                onlyInViewport: true,
            },
            autoplay: slideCount > 1 && !prefersReducedMotion ? {
                delay: 5500,
                disableOnInteraction: false,
                pauseOnMouseEnter: false,
            } : false,
            a11y: {
                enabled: true,
            },
        });
    });
};

initHomeHeroSlider();
