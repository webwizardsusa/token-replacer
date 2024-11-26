
let loadedFiles = [];

function loadStylesheet(file) {
    return new Promise((resolve, reject) => {
        let link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = file.src;
        link.onload = resolve;
        link.onerror = reject;
        document.head.appendChild(link);
    })
}
function loadScript(file) {
    return new Promise((resolve, reject) => {
        let script = document.createElement('script');
        script.src = file.src;
        if (file.module) {
            script.type = 'module';
        }
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    })
}
export default function(files) {
    return new Promise((resolve, reject) => {
        let promises = [];

        files.forEach(file => {
            if (!loadedFiles.includes(file.src)) {
                if (file.type === 'script') {
                    promises.push(loadScript(file));
                } else if (file.type === 'css') {
                    promises.push(loadStylesheet(file));
                }
            }
        })

        Promise.all(promises).then(resolve).catch(reject);
    })
}
