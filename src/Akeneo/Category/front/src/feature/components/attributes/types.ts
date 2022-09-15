import React from 'react';
import {FileInfo} from 'akeneo-design-system';
import {Attribute, CategoryAttributeType, CATEGORY_ATTRIBUTE_TYPE_IMAGE} from '../../models';

export type TextAttributeInputValue = string;

export type ImageAttributeInputValue = FileInfo | null;

export type AttributeInputValue = TextAttributeInputValue | ImageAttributeInputValue;

export type AttributeFieldBuilder<ValueType extends AttributeInputValue> = (
  attribute: Attribute
) => React.FC<AttributeFieldProps<ValueType>>;

export type AttributeFieldProps<ValueType> = {
  locale: string;
  value: ValueType;
  onChange: (value: ValueType) => void;
};

export const isImageAttributeInputValue = (value: AttributeInputValue): value is ImageAttributeInputValue =>
  value !== null && value.hasOwnProperty('originalFilename') && value.hasOwnProperty('filePath');

export const buildDefaultAttributeInputValue = (attributeType: CategoryAttributeType): AttributeInputValue => {
  let value: AttributeInputValue = '';

  switch (attributeType) {
    case CATEGORY_ATTRIBUTE_TYPE_IMAGE: {
      value = {
        filePath: '',
        originalFilename: '',
      };
      break;
    }
  }

  return value;
};
