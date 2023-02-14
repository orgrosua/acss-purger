export function assetPath(assetPath) {
    return assetPath.startsWith('http') ? assetPath : `${acssPurger.assets.url}${assetPath}`;
}