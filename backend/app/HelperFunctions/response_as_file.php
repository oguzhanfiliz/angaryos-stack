<?php

$disk = env('FILESYSTEM_DRIVER', 'uploads');
\Storage::disk($disk)->put($params['fullPath'], json_encode($params['data']));

return \Response::download($params['fullPath'], $params['fileName'], $params['headers']);