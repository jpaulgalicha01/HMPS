
(function () {

    "use strict";


    /*
    |--------------------------------------------------------------------------
    | Module Loader
    |--------------------------------------------------------------------------
    |
    | Handles:
    | 1. Bootstrap tab detection
    | 2. PHP page loading
    | 3. Normal JavaScript loading
    | 4. ES Module loading
    | 5. Prevent duplicate loading
    |
    */


    // ============================================================
    // TRACK LOADED MODULES
    // ============================================================

    const loadedModules = new Set();


    // ============================================================
    // TRACK LOADED SCRIPTS
    // ============================================================

    const loadedScripts = new Set();


    // ============================================================
    // TRACK MODULE INITIALIZATION
    // ============================================================

    const initializedModules = new Set();


    // ============================================================
    // LOAD PHP PAGE
    // ============================================================

    async function loadPage(page, target) {

        if (!page) {
            throw new Error("Module page is not defined.");
        }

        if (!target) {
            throw new Error("Target tab content was not found.");
        }


        // Loading indicator
        target.innerHTML = `
            <div class="text-center p-4 module-loading">

                <div
                    class="spinner-border"
                    role="status">
                </div>

                <div class="mt-2">
                    Loading...
                </div>

            </div>
        `;


        try {

            const response = await fetch(page, {
                method: "GET",

                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },

                cache: "no-cache"
            });


            if (!response.ok) {

                throw new Error(
                    `HTTP ${response.status}: ${response.statusText}`
                );

            }


            const html = await response.text();


            target.innerHTML = html;


            console.log(
                `[ModuleLoader] Page loaded: ${page}`
            );


        } catch (error) {

            console.error(
                `[ModuleLoader] Failed to load page: ${page}`,
                error
            );


            target.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Unable to load module.</strong>
                    <br>
                    Please try again.
                </div>
            `;


            throw error;
        }
    }


    // ============================================================
    // LOAD NORMAL JAVASCRIPT
    // ============================================================

    function loadNormalScript(src) {

        return new Promise((resolve, reject) => {


            if (!src) {

                reject(
                    new Error("Script source is empty.")
                );

                return;
            }


            // Already loaded
            if (loadedScripts.has(src)) {

                console.log(
                    `[ModuleLoader] Script already loaded: ${src}`
                );

                resolve();

                return;
            }


            // Check if script already exists in DOM
            const existingScript =
                document.querySelector(
                    `script[src="${src}"]`
                );


            if (existingScript) {

                loadedScripts.add(src);

                console.log(
                    `[ModuleLoader] Existing script found: ${src}`
                );

                resolve();

                return;
            }


            // Create script
            const script =
                document.createElement("script");


            script.src = src;

            script.type = "text/javascript";

            script.async = false;


            // Successful load
            script.onload = function () {

                loadedScripts.add(src);


                console.log(
                    `[ModuleLoader] Script loaded: ${src}`
                );


                resolve();
            };


            // Failed load
            script.onerror = function () {

                console.error(
                    `[ModuleLoader] Failed to load script: ${src}`
                );


                reject(
                    new Error(
                        `Failed to load script: ${src}`
                    )
                );
            };


            document.head.appendChild(script);

        });
    }


    // ============================================================
    // LOAD ES MODULE
    // ============================================================

    async function loadModuleScript(src) {

        if (!src) {

            throw new Error(
                "Module script source is empty."
            );
        }


        // Already loaded
        if (loadedScripts.has(src)) {

            console.log(
                `[ModuleLoader] Module already loaded: ${src}`
            );

            return null;
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | Dynamic import
            |--------------------------------------------------------------------------
            |
            | This is equivalent to:
            |
            | <script type="module" src="..."></script>
            |
            */

            const module = await import(src);


            loadedScripts.add(src);


            console.log(
                `[ModuleLoader] ES Module loaded: ${src}`
            );


            return module;


        } catch (error) {

            console.error(
                `[ModuleLoader] Failed to load ES Module: ${src}`,
                error
            );


            throw error;
        }
    }


    // ============================================================
    // LOAD ALL MODULE SCRIPTS
    // ============================================================

    async function loadScripts(scripts) {

        if (!Array.isArray(scripts)) {
            return [];
        }


        const loadedModuleObjects = [];


        /*
        |--------------------------------------------------------------------------
        | Load scripts sequentially
        |--------------------------------------------------------------------------
        |
        | This is intentional.
        |
        | Example:
        |
        | common.js
        | area-service.js
        | area-setup.js
        |
        | area-setup.js will only load after the previous
        | scripts have finished.
        |
        */

        for (const scriptConfig of scripts) {

            if (!scriptConfig) {
                continue;
            }


            let src = "";
            let type = "normal";


            // Support object configuration
            if (typeof scriptConfig === "object") {

                src = scriptConfig.src;

                type =
                    scriptConfig.type || "normal";

            }


            // Also support simple string
            else if (typeof scriptConfig === "string") {

                src = scriptConfig;

            }


            if (!src) {

                console.warn(
                    "[ModuleLoader] Invalid script configuration:",
                    scriptConfig
                );

                continue;
            }


            try {

                // ES Module
                if (
                    type === "module" ||
                    type === "text/module"
                ) {

                    const module =
                        await loadModuleScript(src);


                    if (module) {

                        loadedModuleObjects.push({
                            src: src,
                            module: module
                        });

                    }

                }


                // Normal JavaScript
                else {

                    await loadNormalScript(src);

                }

            } catch (error) {

                console.error(
                    `[ModuleLoader] Error loading ${src}`,
                    error
                );

                throw error;
            }
        }


        return loadedModuleObjects;
    }


    // ============================================================
    // INITIALIZE ES MODULE
    // ============================================================

    function initializeESModule(
        moduleName,
        loadedModuleObjects
    ) {

        if (!Array.isArray(loadedModuleObjects)) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Find exported init() function
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | export function init() {
        |
        |     console.log("Area initialized");
        |
        | }
        |
        */

        for (const item of loadedModuleObjects) {

            if (!item || !item.module) {
                continue;
            }


            const module = item.module;


            if (
                typeof module.init === "function"
            ) {

                console.log(
                    `[ModuleLoader] Initializing: ${moduleName}`
                );


                try {

                    module.init();

                } catch (error) {

                    console.error(
                        `[ModuleLoader] Error initializing ${moduleName}`,
                        error
                    );

                }
            }
        }
    }


    // ============================================================
    // INITIALIZE NORMAL MODULE
    // ============================================================

    function initializeNormalModule(moduleName) {

        /*
        |--------------------------------------------------------------------------
        | Optional global initialization
        |--------------------------------------------------------------------------
        |
        | Normal JS modules can register themselves:
        |
        | window.AppModules = window.AppModules || {};
        |
        | window.AppModules["barangay-setup"] = {
        |
        |     init: function () {
        |
        |     }
        |
        | };
        |
        */


        if (
            window.AppModules &&
            window.AppModules[moduleName]
        ) {

            const module =
                window.AppModules[moduleName];


            if (
                typeof module.init === "function"
            ) {

                console.log(
                    `[ModuleLoader] Initializing: ${moduleName}`
                );


                try {

                    module.init();

                } catch (error) {

                    console.error(
                        `[ModuleLoader] Initialization error: ${moduleName}`,
                        error
                    );

                }
            }
        }
    }


    // ============================================================
    // LOAD MODULE
    // ============================================================

    async function loadModule(tab) {

        if (!tab) {
            return;
        }


        const moduleName =
            tab.dataset.module;


        const page =
            tab.dataset.page;


        const targetSelector =
            tab.dataset.bsTarget;


        if (!moduleName) {

            console.error(
                "[ModuleLoader] data-module is missing."
            );

            return;
        }


        if (!targetSelector) {

            console.error(
                `[ModuleLoader] data-bs-target is missing for ${moduleName}`
            );

            return;
        }


        const target =
            document.querySelector(
                targetSelector
            );


        if (!target) {

            console.error(
                `[ModuleLoader] Target not found: ${targetSelector}`
            );

            return;
        }


        // ========================================================
        // CHECK IF MODULE WAS ALREADY LOADED
        // ========================================================

        if (loadedModules.has(moduleName)) {

            console.log(
                `[ModuleLoader] Module already loaded: ${moduleName}`
            );

            return;
        }


        console.log(
            `[ModuleLoader] Loading module: ${moduleName}`
        );


        try {

            // ====================================================
            // LOAD PHP PAGE
            // ====================================================

            await loadPage(
                page,
                target
            );


            // ====================================================
            // GET SCRIPT CONFIGURATION
            // ====================================================

            let scripts = [];


            try {

                scripts =
                    JSON.parse(
                        tab.dataset.scripts || "[]"
                    );

            } catch (error) {

                console.error(
                    `[ModuleLoader] Invalid data-scripts for ${moduleName}`,
                    error
                );

                throw new Error(
                    "Invalid module script configuration."
                );
            }


            // ====================================================
            // LOAD SCRIPTS
            // ====================================================

            const loadedModuleObjects =
                await loadScripts(scripts);


            // ====================================================
            // INITIALIZE MODULE
            // ====================================================

            initializeESModule(
                moduleName,
                loadedModuleObjects
            );


            initializeNormalModule(
                moduleName
            );


            // ====================================================
            // MARK MODULE AS LOADED
            // ====================================================

            loadedModules.add(moduleName);


            initializedModules.add(moduleName);


            console.log(
                `[ModuleLoader] Module ready: ${moduleName}`
            );

        } catch (error) {

            console.error(
                `[ModuleLoader] Failed to load module: ${moduleName}`,
                error
            );


            target.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Failed to load module.</strong>
                    <br>
                    Module: ${moduleName}
                    <br>
                    <small>
                        Please contact the administrator if the problem persists.
                    </small>
                </div>
            `;
        }
    }


    // ============================================================
    // BOOTSTRAP TAB EVENT
    // ============================================================

    document.addEventListener(
        "shown.bs.tab",
        function (event) {

            const tab =
                event.target;


            loadModule(tab);

        }
    );


    // ============================================================
    // LOAD INITIAL ACTIVE TAB
    // ============================================================

    document.addEventListener(
        "DOMContentLoaded",
        function () {

            const activeTab =
                document.querySelector(
                    "#moduleTabs .nav-link.active"
                );


            if (activeTab) {

                loadModule(activeTab);

            }

        }
    );


    // ============================================================
    // OPTIONAL PUBLIC API
    // ============================================================

    window.ModuleLoader = {

        loadModule: loadModule,

        loadPage: loadPage,

        loadScripts: loadScripts,

        loadNormalScript: loadNormalScript,

        loadModuleScript: loadModuleScript,

        isModuleLoaded: function (moduleName) {

            return loadedModules.has(
                moduleName
            );

        },

        isScriptLoaded: function (src) {

            return loadedScripts.has(
                src
            );

        }

    };


})();
