# Base58 Encoding and Decoding Library for PHP

[![Build Status](https://travis-ci.org/stephen-hill/base58php.png)](https://travis-ci.org/stephen-hill/base58php)
[![Packagist Release](http://img.shields.io/packagist/v/stephenhill/base58.svg)](https://packagist.org/packages/stephenhill/base58)
[![MIT License](http://img.shields.io/packagist/l/stephenhill/base58.svg)](https://github.com/stephen-hill/base58php/blob/master/license)
[![Flattr this](https://api.flattr.com/button/flattr-badge-large.png)](https://flattr.com/submit/auto?user_id=stephen-hill&url=https%3A%2F%2Fgithub.com%2Fstephen-hill%2Fbase58php)

## Long Term Support

Each major version of this library will be supported for 5 years after it's initial release. Support will be provided for security and bug fixes.

Version 1 will therefore be supported until the 11th September 2019.

## Background

I wanted a replacement for Base64 encoded strings and the [Base58 encoding used by Bitcoin](https://en.bitcoin.it/wiki/Base58Check_encoding) looked ideal. I looked around for an existing PHP library which would directly convert a string into Base58 but I couldn't find one, or at least one that worked correctly and was also well tested.

So I decided to create a library with the following goals:

- Encode/Decode PHP Strings
- Simple and easy to use
- Fully Tested
- Available via Composer

## Requirements

This library has the following requirements:

- PHP => 5.3
- BC Math Extension

## Installation

I r