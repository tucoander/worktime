<?php

namespace Apontamentos;

class Routes {
    public $pages;
    public $paths;
    public $libs;
    
    public function __construct($folder) {
        $this->pages = array(
            "Home" => $folder."pages/home/",
            "Lançamentos" => array(
                "Inserir" => $folder."src/pages/apontamento/usuario/inserir/",
                "Tabela" => $folder."src/pages/apontamento/usuario/consultar/",
                "Gráfico" => $folder."src/pages/apontamento/usuario/editar/",
            ), 
            "Dashboard" => array(
                "Disponibilidade Padrão" => $folder."src/pages/dashboard/standard/",
                "Disponibilidade Ponderada" => $folder."src/pages/dashboard/ponderado/",
                "Operações" =>  $folder."src/pages/dashboard/operacao/",
            ),
            "Gestor" => array(
                "Inserir lançamentos" => $folder."src/pages/apontamento/gestor/inserir/",
                "Consultar Lançamento" => $folder."src/pages/apontamento/gestor/consultar/",
            )
        );

        $this->paths = array(
            "CSS" => array(
                "loader"=> $folder."src/assets/css/loader.css",
                "navbar"=> $folder."src/assets/css/navbar.css",
                "main"=> $folder."src/assets/css/main.css",
            ),
            "JS" => array(),
            "Database" => $folder."src/database/apontamentos.sqlite",
            "Config" => $folder."src/assets/config/config.json",
        );

        $this->libs = array(
            "ChartJS" => array(
                "css"=> $folder."src/assets/chart/css/loader.css",
                "js"=> $folder."src/assets/chart/js/Chart.js"
            ),
        );
    }
}