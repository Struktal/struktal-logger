<?php

namespace struktal\Logger;

enum LogLevel: int {
    case NONE = 0;
    case FATAL = 1;
    case ERROR = 2;
    case WARN = 3;
    case INFO = 4;
    case DEBUG = 5;
    case TRACE = 6;
}
