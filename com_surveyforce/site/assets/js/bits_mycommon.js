function sf_getObj(name) {
    if (document.getElementById) {
        return document.getElementById(name);
    }
    else if (document.all) {
        return document.all[name];
    }
    else if (document.layers) {
       return document.layers[name];
    }
}