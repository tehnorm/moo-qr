<?php

require_once('./phpqrcode/qrlib.php');


$script_name = $argv[0];

$options = getopt('hlg', array('help','list','generate'));
foreach ($options as $optkey => $optval) {
        switch ($optkey) {
                case 'h' :
                case 'help' :
                        help();
                        exit(0);
                        break;

                case 'g' :
                        if(count($argv) != 5){
                                echo "Incorrect syntax. \n";
                                help();
                                exit(0);
                        }
                        generate($argv[2], $argv[3], $argv[4]);
                        exit(0);
                        break;

                default:
                        echo "Choose a valid option. \n";
                        help();
                        exit(0);

        }
}

// Show help and exit if no options
if(count($options) <= 0){
        help();
        exit(0);
}




function help() {

        global $script_name;

        echo <<< HELP

Usage: {$script_name}
   [-h|--help]
   [-g|--generate <base_url> <count> <ouput_dir>]

Options:
    -h --help        Display this help message
    -g --generate    Generate QR codes as 283 x 283 pixel 300dpi pngs


HELP;

}


function generate($prefix_url, $count, $output_dir){
//         public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false) ^M
	for($i = 1; $count >= $i; $i++){
		$id = generate_id();
		$filename = $output_dir.'/'.$id.'.png';
		qrcode::png('http://'.$prefix_url.'/'.$id, $filename, 'h', 4, 2);

		$thumb = imagecreatetruecolor(283, 283);
		$source = imagecreatefrompng($filename);
		// Resize
		imagecopyresized($thumb, $source, 0, 0, 0, 0, 283, 283, 132, 132);

		// Write the qr url onto the code
		$textcolor = imagecolorallocate($thumb, 158,158,158);
		imagestring($thumb, 5, 63, 266, $prefix_url.'/'.$id, $textcolor);

		// Output
		imagepng($thumb, $filename);
	}
}


function generate_id(){
	static $generated = array();
	while(true){
		$id = uniqid();
		$id = substr($id, 7, 6);
		if(!in_array($id, $generated)){
			$generated[] = $id;
			break;
		}
	}
	return $id;
}
