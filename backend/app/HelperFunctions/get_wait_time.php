<?php
global $pipe;
return microtime(TRUE) - $pipe['laravelStart'];