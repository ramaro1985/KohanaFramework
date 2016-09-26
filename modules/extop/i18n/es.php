<?php defined('SYSPATH') or die('No direct script access.');

return array (
    'please_wait'           => 'Por favor espere',
    'server_error'          => 'Ha ocurrido un error en el servidor.',
    'connection_failed'     => '¡Falló la connección al servidor!',
    'close_all'             => 'Cerrar Todo',
    'minimize_all'          => 'Minimizar Todo',
    'cascade'               => 'Cascada',
    'tile'                  => 'Mosaico',
    'checkerboard'          => 'Álbum',
    'snap_fit'              => 'Mosaico Horizontal',
    'snap_fit_vertical'     => 'Mosaico Vertical',
    'start_menu_label'      => 'Inicio',
    'loading_wallpaper'     => 'Cargando Fondo de Escritorio',
    'loaded_wallpaper'      => 'Fondo de Escritorio Cargado',
    'wallpaper_position'    => 'How should the wallpaper be positioned?',
    'background_color'      => 'Choose a background color',
    'background'            => 'Desktop Background',
    'preferences'           => 'Preferencias del Escritorio',
    'nav'                   => "
        [{ cls: 'icon-pref-shortcut',
            id: 'viewShortcuts',
            text: 'Accesos Directos',
            description: 'Escoja que aplicaciones tendrán iconos de acceso directo en el escritorio'
        },
        { cls: 'icon-pref-autorun',
            id: 'viewAutoRun',
            text: 'Auto - Ejecutar',
            description: 'Escoja que aplicaciones se auto-ejecutarán al cargar el escritorio'
        },
        { cls: 'icon-pref-quickstart',
            id: 'viewQuickstart',
            text: 'Barra de Inicio Rápido',
            description: 'Escoja que aplicaciones se mostrarán en la barra de \"Inicio Rápido\"'
        },
        { cls: 'icon-pref-appearance',
            id: 'viewAppearance',
            text: 'Apariencia',
            description: 'Fine tune window color and style of your windows'
        },
        { cls: 'icon-pref-wallpaper',
            id: 'viewWallpapers',
            text: 'Fondo de Escritorio',
            description: 'Escoja entre las imágenes y colores disponibles para decorar el fondo de su escritorio'
        }]
    ",
    'autorun'   => 'Auto Run',
    'autorun_tip' => "
        Las aplicaciones seleccionadas se cargarán y ejecutarán cuando se cargue el escritorio.
        No olvide presionar \"Guardar\" para persistir los cambios.<br /><br />
        <b>Nota:</b><br />La carga del escritorio se hace más lenta entre más aplicaciones haya seleccionado.
    ",
    'quickstart_tip' => "Las aplicaciones seleccionadas estarán disponibles en la barra de \"Inicio Rápido\".
        No olvide presionar \"Guardar\" para persistir los cambios.
    ",
);