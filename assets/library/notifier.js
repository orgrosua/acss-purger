import 'awesome-notifications/dist/style.css';
import AWN from 'awesome-notifications';

import iconAsync from '../images/icons/gear-solid.svg';
import iconSuccess from '../images/icons/circle-check-solid.svg';
import iconTip from '../images/icons/circle-question-solid.svg';
import iconInfo from '../images/icons/circle-info-solid.svg';
import iconWarning from '../images/icons/circle-exclamation-solid.svg';
import iconAlert from '../images/icons/triangle-exclamation-solid.svg';
import { assetPath } from './assetsHelper';

export function useNotifier(options = {}) {
    return new AWN(Object.assign({
        icons: {
            prefix: ``,
            suffix: ``,

            tip: `<img class="icon-tip" src="${assetPath(iconTip)}"/>`,
            async: `<img class="icon-async" src="${assetPath(iconAsync)}"/>`,
            info: `<img class="icon-info" src="${assetPath(iconInfo)}"/>`,
            success: `<img class="icon-success" src="${assetPath(iconSuccess)}"/>`,
            warning: `<img class="icon-warning" src="${assetPath(iconWarning)}"/>`,
            alert: `<img class="icon-alert" src="${assetPath(iconAlert)}"/>`,

        }
    }, options));
}