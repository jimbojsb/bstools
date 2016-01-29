<?php

namespace Bstools\Renderer;

class Raw implements RendererInterface
{
    /**
     * @return string
     */
    public function render(array $data)
    {
        $output = array();

        foreach ($data as $tubename => $tubedata) {
            foreach ($tubedata as $title => $value) {
                $output[] = sprintf("%s\t%s\t%d", $tubename, $title, $value);
            }
        }

        return implode("\n", $output);
    }
}
