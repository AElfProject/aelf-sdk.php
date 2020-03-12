        }
            } else {
                $constraintObjects = $this->parseConstraint($andConstraints[0]);
            }

            if (1 === count($constraintObjects)) {
                $constraint = $constraintObjects[0];
            } else {
                $constraint = new MultiConstraint($constraintObjects);
            }

            $orGroups[] = $constraint;
        }

        if (1 === count($orGroups)) {
            $constraint = $orGroups[0];
        } elseif (2 === count($orGroups)
            // parse the two OR groups and if they are contiguous we collapse
            // them into one constraint
            && $orGroups[0] instanceof MultiConstraint
            && $orGroups[1] instanceof MultiConstraint
            && 2 === count($orGroups[0]->getConstraints())
            && 2 === count($orGroups[1]->getConstraints())
            && ($a = (string) $orGroups[0])
            && strpos($a, '[>=') === 0 && (false !== ($posA = strpos($a, '<', 4)))
            && ($b = (string) $orGroups[1])
            && strpos($b, '[>=') === 0 && (false !== ($posB = strpos($b, '<', 4)))
            && substr($a, $posA + 2, -1) === substr($b, 4, $posB - 5)
        ) {
            $constraint = new MultiConstraint(array(
                new Constraint('>=', substr($a, 4, $posA - 5)),
                new Constraint('<', substr($b, $posB + 2, -1)),
            ));
        } else {
            $constraint = new MultiConstraint($orGroups, false);
        }

        $constraint->setPrettyString($prettyConstraint);

        return $constraint;
    }

    /**
     * @param string $constraint
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    private function parseConstraint($constraint)
    {
        if (preg_match('{^([^,\s]+?)@(' . implode('|', self::$stabilities) . ')$}i', $constraint, $match)) {
            $constraint = $match[1];
            if ($match[2] !== 'stable') {
                $stabilityModifier = $match[2];
            }
        }

        if (preg_match('{^v?[xX*](\.[xX*])*$}i', $constraint)) {
            return array(new EmptyConstraint());
        }

        $versionRegex = 'v?(\d++)(?:\.(\d++))?(?:\.(\d++))?(?:\.(\d++))?' . self::$modifierRegex . '(?:\+[^\s]+)?';

        // Tilde Range
        //
        // Like wildcard constraints, unsuffixed tilde constraints say that they must be greater than the previous
        // version, to ensure that unstable instances of the current version are allowed. However, if a stability
        // suffix is added to the constraint, then a >= match on the current version is used instead.
        if (preg_match('{^~>?' . $versionRegex . '$}i', $constraint, $matches)) {
            if (strpos($constraint, '~>') === 0) {
                throw new \UnexpectedValueException(
                    'Could not parse version constraint ' . $constraint . ': ' .
                    'Invalid operator "~>", you probably meant to use the "~" operator'
                );
            }

            // Work out which position in the version we are operating at
            if (isset($matches[4]) && '' !== $matches[4] && null !== $matches[4]) {
                $position = 4;
            } elseif (isset($matches[3]) && '' !== $matches[3] && null !== $matches[3]) {
                $position = 3;
            } elseif (isset($matches[2]) && '' !== $matches[2] && null !== $matches[2]) {
                $position = 2;
            } else {
                $position = 1;
            }

            // Calculate the stability suffix
            $stabilitySuffix = '';
            if (empty($matches[5]) && empty($matches[7])) {
                $stabilitySuffix .= '-dev';
            }

            $lowVersion = $this->normalize(substr($constraint . $stabilitySuffix, 1));
            $lowerBound = new Constraint('>=', $lowVersion);

            // For upper bound, we increment the position of one more significance,
            // but highPosition = 0 would be illegal
            $highPosition = max(1, $position - 1);
            $highVersion = $this->manipulateVersionString($matches, $highPosition, 1) . '-dev';
            $upperBound = new Constraint('<', $highVersion);

            return array(
                $lowerBound,
             