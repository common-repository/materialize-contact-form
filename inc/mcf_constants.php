<?php

/**
 * Created by PhpStorm.
 * User: VanIllaSkyPE
 * Date: 15/06/2017
 * Time: 00:47
 */

if (!class_exists("mcf_constants")) {
    /**
     * Class mcf_constants
     */
    class mcf_constants
    {
        //inputs length
        const FIRSTNAMEMIN                          = 1;
        const FIRSTNAMEMAX                          = 50;
        const LASTNAMEMIN                           = 1;
        const LASTNAMEMAX                           = 50;
        const EMAILMIN                              = 5;
        const EMAILMAX                              = 255;
        const SUBJECTMIN                            = 0;
        const SUBJECTMAX                            = 255;
        const MESSAGEMIN                            = 20;
        const MESSAGEMAX                            = 10000;
    }
}