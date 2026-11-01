<?php

namespace Studio\Base;

class PlatformMeta {
    /**
     * The current version ID (increments by 1 for each update)
     */
    const VERSION = 256;

    /**
     * The date this version was built.
     */
    const VERSION_DATE = "May 30, 2024";

    /**
     * The string representation of the current version (in format: x.x.xx)
     */
    const VERSION_STR = "1.87.55";

    /**
     * Data source target for error reporting on this version.
     */
    const DSN = "https://622ff1327be84c26b78286709054fb0e@o90645.ingest.sentry.io/5602780";
}

?>
