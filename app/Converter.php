<?php namespace F16;

    use Illuminate\Support\Facades\File;

Class Converter
    {
        public static function sourceToarray($file, $ext)
        {
            $dArr = null;
            $content = file($file);
            switch ($ext) {
                case 'json':
                    for ($i = 0; $i < count($content); $i++) self::jta($i, $dArr, $content);
                    break;
                case 'csv':
                    $j = 0;
                    for ($i = 0; $i < count($content); $i++) self::cta($i, $dArr, $content, $j);
                    break;
                case 'xml':
                    for ($i = 0; $i < count($content); $i++) self::xta($i, $dArr, $content);
                    break;
                case 'yml':
                    for ($i = 0; $i < count($content); $i++) self::yta($i, $dArr, $content);
                    break;
                default:
                    return false;
            }
            return $dArr;
        }

        public static function jta(&$i, &$dA3, &$content)
    {
        $line = trim($content[$i]);
        if (preg_match('/^".*":.*,\z/', $line)) {
            $s = explode(":", rtrim($line, ','));
            $key = substr($s[0], 1, -1);
            $value = trim($s[1], '\"');
            $dA3 [$key] = $value;
            return true;
        }
        if (preg_match('/".*":\z/', $line)) {
            $s = explode(":", $line);
            $key = substr($s[0], 1, -1);
            $dAr = array();
            while ($i < count($content) - 1) {
                $i++;
                if (trim($content[$i]) == '}') break;
                self::jta($i, $dAr, $content);
            }
            $dA3[$key] = $dAr;
            return true;
        }
        return false;
    }

        public static function cta(&$i, &$dA3, &$content, &$j)
    {
        $line = trim($content[$i]);
        $line = explode(";", $line);
        if ($j >= 0 && strlen($line[$j]) > 0 && (strlen($line[$j + 1]) > 0 || substr($line[$j], -1) == "/")) {
            $key = $line[$j];
            $value = $line[$j + 1];
            $dA3[$key] = $value;
            return true;
        }
        if ($j >= 0 && strlen($line[$j]) > 0 && strlen($line[$j + 1]) == 0) {
            $key = $line[$j];
            $dAr = array();
            $j++;
            while ($i < count($content) - 1) {
                $len = trim($content[$i + 1]);
                if (strlen($len) > 0) {
                    $len = explode(';', $len);
                    if (count($len) > $j && strlen($len[$j]) == 0 || strlen($len[$j - 1]) !== 0) break;
                    $i++;
                    self::cta($i, $dAr, $content, $j);
                }
            }
            $dA3[$key] = $dAr;
            $j--;
            return true;
        }
        if ($j > 0 && strlen($line[$j]) == 0 && strlen($line[$j + 1]) == 0) {
            $j--;
            return true;
        }
        return false;
    }

        public static function xta(&$i, &$dA3, &$content)
    {
        $line = trim($content[$i]);
        if (preg_match('/^\<root\>/', $line)) {
            $dA5 = array();
            while ($i < count($content) && !preg_match('/^\<\/root\>/', trim($content[$i]))) {
                $i++;
                self::xta($i, $dA5, $content);
            }
            $dA3['root'] = $dA5;
            return true;
        }
        if (preg_match('/^<.*>.*<.*>\z/', $line)) {
            $s = explode(">", $line);
            $key = trim($s[0], '<');
            $v = explode("<", $s[1]);
            $value = $v[0];
            $dA3[$key] = $value;
            return true;
        }
        if (preg_match('!^<.*\/>\z!', $line)) {
            $key = trim($line, "<>");
            $dA3[$key] = "";
            return true;
        }
        if (preg_match('/^<[^\/].*>\z/', $line)) {
            $key = ltrim(rtrim($line, ">"), "<");
            $dA4 = array();
            while ($i < count($content) - 1) {
                $i++;
                if (preg_match('`^</' . $key . '>\z`', trim($content[$i]))) break;
                self::xta($i, $dA4, $content);
            }
            $dA3[$key] = $dA4;
            return true;
        }
        return false;
    }

        public static function yta(&$i, &$dA3, &$content)
    {
        $line = trim($content[$i]);
        if (preg_match('/^\<yml_catalog.*\>/', $line)) {
            $key = ltrim(rtrim($line, ">"), "<");
            $dA5 = array();
            while ($i < count($content) && !preg_match('/^\<\/yml_catalog\>/', trim($content[$i]))) {
                $i++;
                self::yta($i, $dA5, $content);
            }
            $dA3[$key] = $dA5;
            return true;
        }
        if (preg_match('/^<.*>.*<.*>\z/', $line)) {
            $s = explode(">", $line);
            $key = trim($s[0], '<');
            $v = explode("<", $s[1]);
            $value = $v[0];
            $dA3[$key] = $value;
            return true;
        }
        if (preg_match('/^<[^\/].*[^\/]>\z/', $line)) {
            $key = ltrim(rtrim($line, ">"), "<");
            $dA4 = array();
            while ($i < count($content) - 1) {
                if (preg_match('`^</' . $key . '>\z`', trim($content[$i + 1]))) break;
                $i++;
                self::yta($i, $dA4, $content);
            }
            $dA3[$key] = $dA4;
            return true;
        }
        if (preg_match('/^<.*\/>\z/', $line)) {
            $key = ltrim(rtrim($line, ">"), "<");
            $dA3[$key] = '';
            return true;
        }
        if (preg_match('/CDATA/', $line)) {
            $key = 'CDATA';
            $dA7 = array();
            while ($i < count($content) - 1) {
                $n = 0x5b;
                if (trim($content[$i + 1]) == ']]>') break;
                $i++;
                self::yta($i, $dA7, $content);
            }
            $dA3[$key] = $dA7;
        }
        return false;
    }

        public static function arrayToFile($source, $cExt, $fileName)
        {
            $uploadPath = 'uploads/';
            switch ($cExt) {
                case 'json':
                    $data = self::arrayTojson($source, $fileName, $uploadPath, $cExt);
                    break;
                case 'xml':
                    $data = self::arrayToxml($source, $fileName, $uploadPath, $cExt);
                    break;
                case 'csv':
                    $data = self::arrayTocsv($source, $fileName, $uploadPath, $cExt);
                    break;
                case 'yml':
                    $data = self::arrayToyml($source, $fileName, $uploadPath, $cExt);
                    break;
                default:
                    return false;
            }
            return $data;
        }

        private static function arrayTojson($source, $fileName, $uploadPath, $cExt)
        {
            $filePath = $uploadPath . $fileName . '.' . $cExt;
            function putTojson(&$source, &$data, &$tab)
            {
                foreach ($source as $key => $elem) {
                    if (is_array($elem)) {
                        $data .= $tab;
                        $tab .= "\t";
                        $data .= '"' . $key . '":' . "\n" . $tab . "{\n";
                        putTojson($elem, $data, $tab);
                        $data .= $tab . "}\n";
                        $tab = substr($tab, 0, -1);
                    } else {
                        $data .= $tab . '"' . $key . '":"' . $elem . '",' . "\n";
                    }
                }

            }

            $tab = '';
            $data = '';
            putTojson($source, $data, $tab);
            File::put(public_path($filePath), $data);
            return $filePath;
        }

        private static function arrayToxml($source, $fileName, $uploadPath, $cExt)
        {
            $filePath = $uploadPath . $fileName . '.' . $cExt;
            function putToxml(&$source, &$data, &$tab)
            {
                foreach ($source as $key => $elem) {
                    if (is_array($elem)) {
                        $data .= $tab;
                        $tab .= "\t";
                        if ($key !== 'CDATA') $data .= '<' . $key . '>' . "\n";
                        else $data .= '<![' . $key . '[' . "\n";
                        putToxml($elem, $data, $tab);
                        $k = explode(" ", $key);
                        if (count($k) > 1) $key = $k[0];
                        $tab = substr($tab, 0, -1);
                        if (!preg_match("/^.xml.*/i", $key)/*$key !== "?xml"*/) if ($key !== 'CDATA') $data .= $tab . "</" . $key . ">\n"; else $data .= $tab . "]]>\n";
                    } else {
                        if (!preg_match('!.*\/\z!', $key)/*$elem !== '+++'*/) {
                            $data .= $tab . '<' . $key . '>' . $elem . '</';
                            $k = explode(" ", $key);
                            if (count($k) > 1) $key = $k[0];
                            $data .= $key . '>' . "\n";
                        } else $data .= $tab . '<' . $key . '>' . "\n";
                    }
                }
            }

            $tab = '';
            foreach ($source as $key => $elem) {
                if (preg_match("/^.xml version=.*/i", $key)) $data = "";
                else $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                break;
            }
            putToxml($source, $data, $tab);
            File::put(public_path($filePath), $data);
            return $filePath;
        }

        private static function arrayTocsv($source, $fileName, $uploadPath, $cExt)
        {
            $filePath = $uploadPath . $fileName . '.' . $cExt;
            function putTocsv(&$source, &$data, &$tab)
            {
                foreach ($source as $key => $elem) {
                    if (is_array($elem)) {
                        $data .= $tab;
                        $tab .= ";";
                        $data .= '' . $key . ';;' . "\n";
                        putTocsv($elem, $data, $tab);
                        $tab = substr($tab, 0, -1);
                    } else $data .= $tab . $key . ';' . $elem . ";\n";
                }
            }

            $tab = '';
            $data = "";
            putTocsv($source, $data, $tab);
            File::put(public_path($filePath), $data);
            return $filePath;
        }

        private static function arrayToyml($source, $fileName, $uploadPath, $cExt)
        {
            $filePath = $uploadPath . $fileName . '.' . $cExt;
            function putToyml(&$source, &$data, &$tab)
            {
                foreach ($source as $key => $elem) {
                    if (is_array($elem)) {
                        $data .= $tab;
                        $tab .= "\t";
                        if ($key !== 'CDATA') $data .= '<' . $key . '>' . "\n";
                        else $data .= '<![' . $key . '[' . "\n";
                        putToyml($elem, $data, $tab);
                        $k = explode(" ", $key);
                        if (count($k) > 1) $key = $k[0];
                        $tab = substr($tab, 0, -1);
                        if (!preg_match("/^.xml.*/i", $key)) if ($key !== 'CDATA') $data .= $tab . "</" . $key . ">\n"; else $data .= $tab . "]]>\n";
                    } else {
                        if (!preg_match('!.*\/\z!', $key)) {
                            $data .= $tab . '<' . $key . '>' . $elem . '</';
                            $k = explode(" ", $key);
                            if (count($k) > 1) $key = $k[0];
                            $data .= $key . '>' . "\n";
                        } else $data .= $tab . '<' . $key . '>' . "\n";
                    }
                }
            }

            $tab = '';
            foreach ($source as $key => $elem) {
                if (preg_match("/^.xml version=.*/i", $key)) $data = "";
                else $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                break;
            }
            putToyml($source, $data, $tab);
            File::put(public_path($filePath), $data);
            return $filePath;
        }
    }