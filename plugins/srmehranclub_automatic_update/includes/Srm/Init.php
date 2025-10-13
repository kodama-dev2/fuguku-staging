<?php

final class Srm_Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_services()
    {
        return [
            Srm_Base::class,
            Srm_Setup::class,
			Srm_License::class,
			Srm_Dashboard::class,
			Srm_Files::class,
			Srm_Whitelebel::class,
			Srm_Activities::class,
            Srm_ApiHandler::class,
            Srm_CheckUpdates::class,
            Srm_RobustThemeInstaller::class
        ];
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     * @return
     */
    public static function register_services()
    {
        foreach ( self::get_services() as $class ) {
            $service = self::instantiate( $class );
            if ( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param  class $class    class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate( $class )
    {
        $service = new $class();
        return $service;
    }
}
