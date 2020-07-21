function hasTypeOfAddressComponent(component, type) {
    var hasType = false;

    if (component.types.length) {
        for (var j = 0; j < component.types.length; j++) {
            if (component.types[j] == type) {
                hasType = true;
                break;
            }
        }
    }

    return hasType;
}
