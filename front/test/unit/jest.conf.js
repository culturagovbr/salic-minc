const path = require('path');

module.exports = {
    rootDir: path.resolve(__dirname, '../../../'),
    moduleFileExtensions: [
        'js',
        'json',
        'vue',
    ],
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/front/src/$1',
    },
    transform: {
        '^.+\\.js$': '<rootDir>/node_modules/babel-jest',
        '.*\\.(vue)$': '<rootDir>/node_modules/vue-jest',
    },
    testPathIgnorePatterns: [
        '<rootDir>/front/test/e2e',
    ],
    snapshotSerializers: ['<rootDir>/node_modules/jest-serializer-vue'],
    setupFiles: ['<rootDir>/front/test/unit/setup'],
    coverageDirectory: '<rootDir>/front/test/unit/coverage',
    testEnvironment: 'node',
};
