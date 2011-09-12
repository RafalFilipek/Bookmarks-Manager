var Router = {
    
    routes : {"GET___fetch_profiler_data":{"pattern":"\/__fetch_profiler_data","requirements":[],"defaults":{"_controller":{}},"variables":[]},"index":{"pattern":"\/","requirements":[],"defaults":{"_controller":{}},"variables":[]},"mark_category":{"pattern":"\/kategorie\/{id}\/ustaw-zaznaczenie\/{bool}","requirements":{"id":"\\d+"},"defaults":{"_controller":{}},"variables":["id","bool"]}},

    get: function(name, params) {
        if (this.routes[name]) {
            params = params == undefined ? {} : params;
            var route = this.routes[name],
                requirements = route.requirements,
                defaults = route.defaults,
                variables = route.variables,
                result = route.pattern,
                val;
            for (param in variables) {
                param = variables[param];
                val = params[param] != undefined ? params[param] : defaults[param];
                if (val == undefined) {
                    throw 'Missing "'+param+'" parameter for route "'+name+'"!';
                }
                if (requirements[param] && !new RegExp(requirements[param]).test(val)) {
                    throw 'Parameter "'+param+'" for route "'+name+'" must pass "'+requirements[param]+'" test!';
                }
                result = result.replace('{'+param+'}', val);
            }
            return result;
        } else {
            throw 'Undefined route "'+name+'"!';
        }
    }

}

