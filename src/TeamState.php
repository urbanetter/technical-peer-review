<?php

namespace App;

enum TeamState: string
{
    case PEER_FEEDBACK = 'peer feedback';
    case CONFIDENCE = 'confidence';

}